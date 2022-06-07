<?php

include_once 'zettle.common.php';

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

    if ($event['eventName'] == 'PurchaseCreated') {
        $payload = json_decode(json_decode(json_encode($event['payload']), true), true);
        $amount = ($payload['amount'] / 100);
        $currency = $payload['currency'];
        // Other Feilds can be added to this
        $paymentData = [
            'formatted_amount' => number_format($amount, 2) . ' ' . $currency,
            'amount' => $amount,
            'timestamp' => $payload['timestamp'],
            'userDisplayName' => $payload['userDisplayName']
        ];

        // Get currentTransactions
        $currentTransactions = convertAndGetSettings('zettle-transactions');
        // Push new transaction
        array_push($currentTransactions, $paymentData);
        // Store transaction to json file
        writeToJsonFile('transactions', $currentTransactions);
        // Get zettle config
        $config = convertAndGetSettings('zettle');
        // Check an command has set
        if ($config['command'] != '') {
            // Build command url from selected command on setup page
            $url = 'http://' . $_SERVER['SERVER_NAME'] . '/api/command/'.urlencode($config['command']);
            // Get command args
            $data = $config['args'];
            // Check if command is "Overlay Model Effect"
            if ($config['command'] == 'Overlay Model Effect') {
                // Find and replace vaules in array as payment details
                $text = str_replace([
                    '{{PAYER_NAME}}',
                    '{{AMMOUNT}}'
                ], [
                    $paymentData['userDisplayName'],
                    $paymentData['formatted_amount']
                ], end($data));
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
        }
        return true;
    }
    return true;
}
