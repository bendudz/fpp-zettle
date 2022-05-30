<?php

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

function readConfig()
{
    $url = "http://localhost/api/configfile/plugin.fpp-zettle.json";
    $config = file_get_contents($url);
    $config = utf8_encode($config);
    return json_decode($config);
}

# POST /api/plugin/fpp-zettle/event
# This is the endpoint we will need users to register with the Zettle dev portal
# https://my.zettle.com/apps/api-keys > click create api key > select READ:USER_INFO & READ:PURCHASE
# Copy The client id & secret to a notepad or something they are needed for the plugin.
function fppZettleEvent()
{
    return true;

    $event = json_decode(file_get_contents('php://input'), true);
    header("Content-Type: application/json");
    // TODO trigger an action or effect or custom script
    // Triggering a command we can just call the API check http://{fpp_address}/apihelp.php#commands
    $input = readConfig()['trigger'];
    // TODO do we need to support a start channel other than 0?
    $url = 'http://localhost/api/command/Effect Start?effect=' . $input . '&startChannel=0&loop=false&bg=true&ifNotRunning=false';
    $trigger_effect = file_get_contents($url);

    return true;
    //    return $event['payload'];
}
