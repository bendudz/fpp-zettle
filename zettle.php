<?php
include_once "/opt/fpp/www/common.php";
include_once 'zettle.common.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$a = session_id();
if (empty($a)) {
    session_start();
}
$_SESSION['session_id'] = session_id();

$command_array = array(
  'login' => 'LoginUser',
  'subscriptions' => 'GetSubscriptions',
  'create_subscription' => 'CreatePurchaseSubscription',
  'get_org_id' => 'GetOrgId',
  'delete_subscription' => 'DeleteSubscription',
  'clear_config' => 'ClearConfig',
  'clear_subscription' => 'ClearSubscription',
  'matrix_text' => 'MatrixText'
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

function buildQuery($data = [], $o = [], $url)
{
    $postdata = http_build_query($data);
    $o['content'] = $postdata;

    if (isset($_SESSION['expires_in']) && isset($_SESSION['access_token'])) {
        if (time() >= $_SESSION['expires_in']) {
            $login = LoginUser();
            if ($login['error']) {
                return [
                    'error' => true,
                    'message' => $login['message']
                ];
            }
            $access_token = $login['access_token'];
        } else {
            $access_token = $_SESSION['access_token'];
        }
    } else {
        $login = LoginUser();
        if ($login['error']) {
            return [
                'error' => true,
                'message' => $login['message']
            ];
        }
        $access_token = $login['access_token'];
    }

    $o['header'] = 'Authorization: Bearer '.$access_token;

    $opts = array('http' => $o);
    $context = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    $jsonResult = json_decode($result);
    // $jsonResult['error'] = false;
    return $jsonResult;
}

function httpPost($url, $data, $headers, $auth = false, $json = false)
{
    if ($auth) {
        if (isset($_SESSION['expires_in']) && isset($_SESSION['access_token'])) {
            if (time() >= $_SESSION['expires_in']) {
                $login = LoginUser();
                if ($login['error']) {
                    return json_encode([
                        'error' => true,
                        'message' => $login['message']
                    ]);
                }
                $headers[] = "Authorization: Bearer " . $login['access_token'];
            } else {
                $headers[] = "Authorization: Bearer ".$_SESSION['access_token'];
            }
        } else {
            $login = LoginUser();
            if ($login['error']) {
                return json_encode([
                    'error' => true,
                    'message' => $login['message']
                ]);
            }
            $headers[] = "Authorization: Bearer " . $login['access_token'];
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

// /plugin.php?plugin=fpp-zettle&page=zettle.php&command=login&nopage=1
function LoginUser()
{
    global $oauth_base;

    $pluginJson = convertAndGetSettings('zettle');

    $query = httpPost(
        $oauth_base.'/token',
        [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'client_id' => $pluginJson['client_id'],
            'assertion' => $pluginJson['client_secret']
        ],
        [
            'Content-Type: application/x-www-form-urlencoded'
        ]
    );

    if (isset($query->error) || isset($query->code)) {
        return[
            'error' => true,
            'message' => isset($query->error) ? $query->error_description : $query->message
        ];
    }

    $access_token = $query->access_token;
    $expires_in = time() + $query->expires_in;

    $_SESSION['access_token'] = $access_token;
    $_SESSION['expires_in'] = $expires_in;

    return [
        'error' => false,
        'access_token' => $access_token
    ];
}

//plugin.php?plugin=fpp-zettle&page=zettle.php&command=subscriptions&nopage=1
function GetSubscriptions()
{
    global $subscriptions_url;

    $query = buildQuery([], [
      'method'  => 'GET',
    ], $subscriptions_url);

    if (isset($query['error'])) {
        echo json_encode([
            'error' => true,
            'message' => $query['message'] . '//Query Error'
        ]);
    } else {
        echo json_encode([
            'error' => false,
            'subscriptions' => $query
        ]);
    }
}

//plugin.php?plugin=fpp-zettle&page=zettle.php&command=create_subscription&nopage=1
function CreatePurchaseSubscription()
{
    global $subscriptions_url;
    global $settings;

    // Get destination from form
    $destination_url = $_POST['destination'];
    // Explode destination_url to get parts that we need
    $explode_destination_url = explode('/', $destination_url);
    // Get the url that we need
    $base_url = $explode_destination_url[2];
    // Check if explode_destination_url last item == event
    if (end($explode_destination_url) == 'event') {
        // Unset first two explode_destination_url items
        unset($explode_destination_url[0]);
        unset($explode_destination_url[1]);
        // Rebuild destination url
        $destination = implode('/', $explode_destination_url);
    } else {
        // Build destination url with added part
        $destination = $base_url . '/api/plugin/fpp-zettle/event';
    }
    // Check for ui password
    if (checkForUIPassword()) {
        // Build username_password
        $username_password = 'admin:' . $settings['password'] . '@';
        // Combine username_password and destination
        $complete_destination_url = $username_password . $destination;
    } else {
        // set destination as password is not found
        $complete_destination_url = $destination;
    }

    $query = httpPost(
        $subscriptions_url,
        [
            'uuid' => $_POST['uuid'],
            "transportName"=> "WEBHOOK",
            "eventNames" => ["PurchaseCreated"],
            "destination" => 'https://' . $complete_destination_url,
            "contactEmail" => $_POST['contactEmail']
            ],
        [
                "Content-type: application/json"
            ],
        true,
        true
    );
    // Convert stdClass object to array
    $data = json_decode(json_encode($query), true);

    if (array_key_exists('errorType', $data)) {
        switch ($data['errorType']) {
            case 'CONSTRAINT_VIOLATION':
                $message = $data['violations'][0]['developerMessage'];
                break;
            default:
                $message = $data['developerMessage'];
        }

        echo json_encode([
            'error' => true,
            'message' => $message
        ]);
    } else {
        echo json_encode([
          'error' => false,
          'message' => 'Purchase Subscription Created',
          'subscription' => $query,
          'organizationUuid' => $_POST['organizationUuid']
        ]);
    }
}

//plugin=fpp-zettle&page=zettle.php&command=get_org_id&nopage=1
function GetOrgId()
{
    global $oauth_base;

    $pluginJson = convertAndGetSettings('zettle');
    // Check if organizationUuid has been saved in config if so use it
    if ($pluginJson['organizationUuid'] !== '') {
        echo json_encode([
            'error' => false,
            'organizationUuid' => $pluginJson['organizationUuid'],
            'message' => 'organizationUuid found in config'
        ]);
    } else {
        $query = buildQuery([], [
        'method' => 'GET',
        ], $oauth_base.'/users/self', true);

        if (isset($query->error)) {
            echo json_encode([
                'error' => true,
                'message' => $query->message . '//Query Error'
            ]);
        } else {
            echo json_encode([
                'error' => false,
                'organizationUuid' => $query->organizationUuid
            ]);
        }
    }
}

function DeleteSubscription($display = true)
{
    global $subscriptions_url;
    $pluginJson = convertAndGetSettings('zettle');

    if (isset($pluginJson['subscriptions']['uuid'])) {
        $query = buildQuery([], [
        'method' => 'DELETE',
        ], $subscriptions_url . '/' . $pluginJson['subscriptions']['uuid']);
    }

    if ($display) {
        echo json_encode([
            'error' => false
        ]);
    } else {
        return true;
    }
}

function ClearConfig()
{
    DeleteSubscription(false);
    setPluginJSON('fpp-zettle', emptyConfig());
    setPluginJSON('fpp-zettle-transactions', []);

    echo json_encode([
        'error' => false
    ]);
}

function ClearSubscription()
{
    $pluginJson = convertAndGetSettings('zettle');
    $pluginJson['subscriptions'] = [];

    DeleteSubscription(false);
    setPluginJSON('fpp-zettle', $pluginJson);
    echo json_encode([
        'error' => false,
        'message' => 'Subscription Cleared!'
    ]);
}

function setPluginJSON($plugin, $js)
{
    global $settings;

    $cfgFile = $settings['configDirectory'] . "/plugin." . $plugin . ".json";
    file_put_contents($cfgFile, json_encode($js, JSON_PRETTY_PRINT));
    // echo json_encode($js, JSON_PRETTY_PRINT);
}

function MatrixText()
{
    $url = 'http://192.168.1.156/api/command/'.urlencode('Overlay Model Effect');

    $data = [
        "Matrix",
        "Enabled",
        "Text",
        "#ffffff",
        "Helvetica",
        "22",
        "true",
        "Right to Left",
        "100",
        "0",
        "Thank You ".$_GET['name']
    ];

    $query = json_encode($data);
    $ch    = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    $response = curl_exec($ch);
    curl_close($ch);
    echo $response;
}
