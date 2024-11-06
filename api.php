<?php

use GuzzleHttp\Client as GuzzleHttpClient;
use LeonardoTeixeira\Pushover\Client;
use LeonardoTeixeira\Pushover\Exceptions\PushoverException;
use LeonardoTeixeira\Pushover\Message;
use LeonardoTeixeira\Pushover\Priority;

include_once 'zettle.common.php';
include 'vendor/autoload.php';

# Add API resources to this file

function getEndpointsfppzettle()
{
    $endpoints = array(
        array(
            'method' => 'GET',
            'endpoint' => 'version',
            'callback' => 'fppZettleVersion'
        ),
        array(
            'method' => 'POST',
            'endpoint' => 'event',
            'callback' => 'fppZettleEvent'
        ),
        array(
            'method' => 'POST',
            'endpoint' => 'app',
            'callback' => 'fppZettleApp'
        )
    );
    return $endpoints;
}

// GET /api/plugin/fpp-zettle/version
function fppZettleVersion()
{
    $result = array();
    $result['version'] = 'fpp-zettle v1.0.0';

    return json_encode($result);
}

# POST /api/plugin/fpp-zettle/event
# This is the endpoint we will need users to register with the Zettle dev portal
# https://my.zettle.com/apps/api-keys > click create api key > select READ:USER_INFO & READ:PURCHASE
# Copy The client id & secret to a notepad or something they are needed for the plugin.
function fppZettleEvent()
{
    $event = json_decode(file_get_contents('php://input'), true);
    header("Content-Type: application/json");
    // Check for eventName in the post data
    if (!isset($event['eventName'])) {
        // eventName could not be found display message to user
        return json_encode(
            [
                'error' => true,
                'message' => 'eventName could not be found in request, please try again'
            ]
        );
    }
    // Check if the right eventName has been sent
    if ($event['eventName'] != 'PurchaseCreated') {
        return json_encode(
            [
                'error' => true,
                'message' => 'eventName needs to be PurchaseCreated',
            ]
        );
    }
    // Get payload from zettle and turn in to an array
    $payload = json_decode(json_decode(json_encode($event['payload']), true), true);
    $amount = ($payload['amount'] / 100);
    $currency = $payload['currency'];

    // Other Feilds can be added to this
    $paymentData = [
        'formatted_amount' => number_format($amount, 2) . ' ' . $currency,
        'amount' => $amount,
        'timestamp' => $payload['timestamp'],
        'userUuid' => $payload['userUuid'],
    ];

    // Get currentTransactions
    $currentTransactions = convertAndGetSettings('zettle-transactions');
    // Push new transaction
    array_push($currentTransactions, $paymentData);
    // Store transaction to json file
    writeToJsonFile('transactions', $currentTransactions);
    // Store transation account
    totalTransactions($amount);
    // Write transaction to log file
    custom_logs($payload);
    // Get zettle config
    $config = convertAndGetSettings('zettle');
    // Check for multiple readers
    if (isset($config['readers']) && count($config['readers']) > 0) {
        // Reader run count
        $reader_run = FALSE;
        // Loop over readers
        foreach ($config['readers'] as $reader) {
            // Check if reader has a command set
            if ($reader['command'] != '') {
                // Check if a reader has the same product as the payload from zettle has
                if ($reader['product'] == $payload['products'][0]['name']) {
                    // Add formatted_amount to reader array
                    $reader['formatted_amount'] = $paymentData['formatted_amount'];
                    // Reader has same product runCommand
                    custom_logs('Run reader command');
                    runCommand($reader);
                    $reader_run = TRUE;
                }
            }
        }
        // Check if a reader has been run
        if ($reader_run == FALSE) {
            // No reader found run default command
            custom_logs('No reader found with assigned product (' . $payload['products'][0]['name'] . ') run default command');
            runCommand([
                'command' => $config['command'],
                'args' => $config['args'],
                'formatted_amount' => $paymentData['formatted_amount'],
            ]);
        }
    } else {
        // No multiple readers found run default command
        if ($config['effect_activate'] == 'yes' && $config['command'] != '') {
            // Run default command
            custom_logs('Run default command');
            runCommand([
                'command' => $config['command'],
                'args' => $config['args'],
                'formatted_amount' => $paymentData['formatted_amount'],
            ]);
        }
    }
    // Check if pushover is active
    if (isset($config['pushover']) && $config['pushover']['activate'] == 'yes') {
        pushover($config, $paymentData);
    }
    // Check if publish is active
    if (isset($config['publish']) && $config['publish']['activate'] == 'yes') {
        publishTransactionDetails($payload);
    }
    // Store userUuid and if they have activated publish or not
    storeCustomer($config, $paymentData);
    return true;
}

function fppZettleApp()
{
    $event = json_decode(file_get_contents('php://input'), true);
    header("Content-Type: application/json");

    $amount = ($event['amount'] / 100);
    $currency = $event['currency'];

    // Other Feilds can be added to this
    $paymentData = [
        'formatted_amount' => number_format($amount, 2) . ' ' . $currency,
        'amount' => $amount,
        'timestamp' => time(),
        'userUuid' => "",
    ];

    // Get currentTransactions
    $currentTransactions = convertAndGetSettings('zettle-transactions');
    // Push new transaction
    array_push($currentTransactions, $paymentData);
    // Store transaction to json file
    writeToJsonFile('transactions', $currentTransactions);
    // Store transation account
    totalTransactions($amount);

    return json_encode(
        [
            'error' => false,
            'message' => 'fpp app api function',
        ]
    );
}

/**
 * Make pushover message and send it
 *
 * @param array $config
 * @param array $paymentData
 * @return void
 */
function pushover($config = [], $paymentData = [])
{
    $build_message = buildMessage($paymentData, $config['pushover']['message']);

    $client = new Client($config['pushover']['user_key'], $config['pushover']['app_token']);
    $message = new Message($build_message, 'FPP CARD READER', Priority::HIGH);

    try {
        $client->push($message);
        custom_logs('The pushover message has been pushed!');
    } catch (PushoverException $e) {
        custom_logs('PUSHOVER ERROR: ', $e->getMessage());
    }
}

/**
 * Publish Transaction Details to fpp-zettle.co.uk
 *
 * @param array $payload
 * @return void
 */
function publishTransactionDetails($payload = [])
{
    $client = new GuzzleHttpClient([
        "base_uri" => "https://fpp-zettle.co.uk",
        'headers' => [
            'Content-Type' => 'application/json'
        ]
    ]);

    $options = [
        'form_params' => [
            'amount' => $payload['amount'],
            'currency' => $payload['currency'],
        ],
    ];

    // if ($config['publish']['location'] == 'yes') {
    //     $gpsCoordinates = $payload['gpsCoordinates'];
    //     $options['latitude'] = $gpsCoordinates['latitude'];
    //     $options['longitude'] = $gpsCoordinates['longitude'];
    // }

    $response = $client->post("/api/transactions", $options);
    custom_logs('Publish Transaction Details to fpp-zettle.co.uk');
    // custom_logs($response->getBody());
}

/**
 * Store custom data on fpp-zettle.co.uk
 *
 * @param array $config
 * @param array $data
 * @return void
 */
function storeCustomer($config = [], $data = [])
{
    $client = new GuzzleHttpClient([
        "base_uri" => "https://fpp-zettle.co.uk",
        'headers' => [
            'Content-Type' => 'application/json'
        ]
    ]);

    $options = [
        'form_params' => [
            'user_uuid' => $data['userUuid'],
            'active' => isset($config['publish']) && $config['publish']['activate'] == 'yes' ? TRUE : FALSE,
        ],
    ];

    $client->post("/api/customers", $options);
}

/**
 * Build message for Overlay Model Effect
 *
 * @param array $paymentData
 * @param array $data command data
 * @return string
 */
function buildMessage($paymentData = [], $data = [], $url_encode = false)
{
    $replacement_values = [
        $paymentData['formatted_amount'],
        runningTotal('everything'),
        runningTotal('today'),
        runningTotal('this_month')
    ];

    if ($url_encode) {
        $replacement_values = array_map('urlencode', $replacement_values);
    }

    // Find and replace values in array as payment details
    $text = str_replace([
        '{{AMOUNT}}',
        '{{EVERYTHING}}',
        '{{TODAY}}',
        '{{THIS_MONTH}}'
    ],  $replacement_values, is_array($data) ? end($data) : $data);

    custom_logs('Build Message Output: ' . $text);
    return $text;
}

/**
 * Run command
 *
 * @param array $data command details
 * @return void
 */
function runCommand($data = [])
{
    // Build command url from selected command
    // $url = 'http://localhost/api/command/' . urlencode($data['command']);
    $url = 'http://localhost/api/command/';
    // Get command args
    $command_args = $data['args'];
    // Check if command is "Overlay Model Effect"
    if ($data['command'] == 'Overlay Model Effect') {
        $text = buildMessage([
            'formatted_amount' => $data['formatted_amount']
        ], $command_args);
        // Remove and replace last item from array
        array_pop($command_args);
        $command_args[] = $text;
    }
    else if ($data['command'] == 'URL') {
        custom_logs("Is URL");
        $updated_url = buildMessage([
            'formatted_amount' => $data['formatted_amount']
        ], $command_args[0], true);
        $command_args[0] = $updated_url;

        $updated_post_body = buildMessage([
            'formatted_amount' => $data['formatted_amount']
        ], $command_args[2]);
        $command_args[2] = $updated_post_body;
    }

    if ($data['command'] != 'Overlay Model Effect') {
        // Write command args back into $data, but only for commands other than Overlay Model Effect,
        // which never used to do it - is this a bug that needs fixing?
        $data['args'] = $command_args;
    }

    custom_logs('Sending command: ' . $data);

    // Fire the command
    $query = json_encode($data);
    $ch    = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_exec($ch);
    curl_close($ch);
    // Write to log file
    custom_logs('command fired');
}
