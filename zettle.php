<?php
include_once "/opt/fpp/www/common.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$a = session_id();
if (empty($a)) {
    session_start();
}
$_SESSION['session_id'] = session_id();

$command_array = array(
  'login' => 'LoginUser',
  'session' => 'GetSessionVaules',
  'subscriptions' => 'GetSubscriptions',
  'create_subscription' => 'CreatePurchaseSubscription',
  'get_org_id' => 'GetOrgId'
);

$command = "";
$args = array();

$oauth_base = "https://oauth.zettle.com";
$pusher_base = "https://pusher.izettle.com";
$subscriptions_url = $pusher_base."/organizations/self/subscriptions";


if (isset($_GET['command']) && !empty($_GET['command'])) {
    $command = $_GET['command'];
    $args = $_GET;
} elseif (isset($_POST['command']) && !empty($_POST['command'])) {
    $command = $_POST['command'];
    $args = $_POST;
}

if (array_key_exists($command, $command_array)) {
    global $debug;

    if ($debug) {
        error_log("Calling " .$command);
    }

    call_user_func($command_array[$command]);
}
return;

function convertAndGetSettings()
{
    global $settings;

    $cfgFile = $settings['configDirectory'] . "/plugin.fpp-zettle.json";
    if (file_exists($cfgFile)) {
        $j = file_get_contents($cfgFile);
        $json = json_decode($j, true);
        return $json;
    }
    $j = "{\"client_id\": \"client_id\", \"client_secret\": \"client_secret\", \"organizationUuid\": \"\", \"subscriptions\": [] }";
    return json_decode($j, true);
}

function buildQuery($data = [], $o = [], $url)
{
    $postdata = http_build_query($data);
    $o['content'] = $postdata;

    if (isset($_SESSION['expires_in']) && isset($_SESSION['access_token'])) {
        if (time() >= $_SESSION['expires_in']) {
            $access_token = LoginUser();
        } else {
            $access_token = $_SESSION['access_token'];
        }
    } else {
        $access_token = LoginUser();
    }

    $o['header'] = 'Authorization: Bearer '.$access_token;

    $opts = array('http' => $o);
    $context = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    $jsonResult = json_decode($result);
    return $jsonResult;
}

function httpPost($url, $data, $headers, $auth = false, $json = false)
{
    if ($auth) {
        if (isset($_SESSION['expires_in']) && isset($_SESSION['access_token'])) {
            if (time() >= $_SESSION['expires_in']) {
                $headers[] = "Authorization: Bearer ".LoginUser();
            } else {
                $headers[] = "Authorization: Bearer ".$_SESSION['access_token'];
            }
        } else {
            $headers[] = "Authorization: Bearer ".LoginUser();
        }
    }

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json ? json_encode($data) : http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response);
}


function LoginUser()
{
    global $oauth_base;

    $pluginJson = convertAndGetSettings();

    $query = httpPost(
        $oauth_base.'/token',
        [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'client_id' => $pluginJson['client_id'],
            'assertion' => $pluginJson['client_secret']
        ],
        [
            'Content-Type: application/x-www-form-urlencoded'
        ],
        false,
        false
    );

    $access_token = $query->access_token;
    $expires_in = time() + $query->expires_in;

    $_SESSION['access_token'] = $access_token;
    $_SESSION['expires_in'] = $expires_in;

    return $access_token;
    // //plugin.php?plugin=fpp-zettle&page=zettle.php&command=login&nopage=1
}

function GetSubscriptions()
{
    global $subscriptions_url;

    $query = buildQuery([], [
      'method'  => 'GET',
    ], $subscriptions_url);

    echo json_encode($query);
    //plugin.php?plugin=fpp-zettle&page=zettle.php&command=subscriptions&nopage=1
}

function CreatePurchaseSubscription()
{
    global $subscriptions_url;

    $query = httpPost(
        $subscriptions_url,
        [
            'uuid' => $_POST['organizationUuid'],
            "transportName"=> "WEBHOOK",
            "eventNames" => ["PurchaseCreated"],
            "destination" => $_POST['destination'],
            "contactEmail" => $_POST['contactEmail']
            ],
        [
                "Content-type: application/json"
            ],
        true,
        true
    );

    if (array_key_exists('errorType', $query)) {
        echo json_encode([
        'error' => true,
        'message' => $query->developerMessage
      ]);
    } else {
        echo json_encode([
          'error' => false,
          'message' => 'Purchase Subscription Created',
          'subscription' => $query,
          'organizationUuid' => $_POST['organizationUuid']
        ]);
    }
    //plugin.php?plugin=fpp-zettle&page=zettle.php&command=create_subscription&nopage=1
}

function GetOrgId()
{
    global $oauth_base;

    $pluginJson = convertAndGetSettings();
    // Check if organizationUuid has been saved in congif if so use it
    if ($pluginJson['organizationUuid'] !== '') {
        echo json_encode([
            'error' => false,
            'organizationUuid' => $pluginJson['organizationUuid']
        ]);
    } else {
        $query = buildQuery([], [
        'method'  => 'GET',
        ], $oauth_base.'/users/self', true);

        echo json_encode([
            'error' => false,
            'organizationUuid' => $query->organizationUuid
        ]);
    }
    //plugin=fpp-zettle&page=zettle.php&command=get_org_id&nopage=1
}
