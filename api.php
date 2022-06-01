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
    global $pluginName;

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

        // TODO trigger an action or effect or custom script
        // Triggering a command we can just call the API check http://{fpp_address}/apihelp.php#commands
        // $input = readConfig()['trigger'];
        // TODO do we need to support a start channel other than 0?
        // $url = 'http://localhost/api/command/Effect Start?effect=' . $input . '&startChannel=0&loop=false&bg=true&ifNotRunning=false';
        // $trigger_effect = file_get_contents($url);

        return true;
    }
    return true;
}
