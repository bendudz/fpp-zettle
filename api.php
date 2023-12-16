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
    // Check if eventName === PurchaseCreated
    if ($event['eventName'] == 'PurchaseCreated') {
        $payload = json_decode(json_decode(json_encode($event['payload']), true), true);
        $amount = ($payload['amount'] / 100);
        $currency = $payload['currency'];
        // Other Feilds can be added to this
        $paymentData = [
            'formatted_amount' => number_format($amount, 2) . ' ' . $currency,
            'amount' => $amount,
            'timestamp' => $payload['timestamp'],
            // 'userDisplayName' => $payload['userDisplayName']
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
        // Check an command has set
        if ($config['effect_activate'] == 'yes' && $config['command'] != '') {
            // Build command url from selected command on setup page
            $url = 'http://localhost/api/command/' . urlencode($config['command']);
            // Get command args
            $data = $config['args'];
            // Check if command is "Overlay Model Effect"
            if ($config['command'] == 'Overlay Model Effect') {
                $text = buildMessage($paymentData, $data);
                // Remove and replace last item from array
                array_pop($data);
                $data[] = $text;
            }
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
        if (isset($config['pushover']) && $config['pushover']['activate'] == 'yes') {
            pushover($config, $paymentData);
        }
        if (isset($config['publish']) && $config['publish']['activate'] == 'yes') {
            publishTransactionDetails($config, $payload);
        }
        return true;
    }
    return true;
}

// function runningTotal($option = 'everything') {
//     return file_get_contents('http://localhost/plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_purchases&nopage=1&option=' . $option);
// }

function pushover($config, $paymentData)
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

function publishTransactionDetails($config, $payload)
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

function buildMessage($paymentData, $data)
{
    // Find and replace values in array as payment details
    $text = str_replace([
        '{{AMOUNT}}',
        '{{EVERYTHING}}',
        '{{TODAY}}',
        '{{THIS_MONTH}}'
    ], [
        $paymentData['formatted_amount'],
        runningTotal('everything'),
        runningTotal('today'),
        runningTotal('this_month')
    ], is_array($data) ? end($data) : $data);

    custom_logs('Build Message Output: ' . $text);

    return $text;
}
