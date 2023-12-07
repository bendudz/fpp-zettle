<?php
include 'vendor/autoload.php';
include_once "/opt/fpp/www/common.php";
include_once 'zettle.common.php';

use GuzzleHttp\Client;

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
    'update_subscription' => 'updatePurchaseSubscription',
    'get_org_id' => 'GetOrgId',
    'delete_subscription' => 'DeleteSubscription',
    'clear_config' => 'ClearConfig',
    'clear_subscription' => 'ClearSubscription',
    'matrix_text' => 'MatrixText',
    'get_purchases' => 'GetPurchases',
    'save_pushover' => 'SavePushover',
    'save_publish' => 'SavePublish',
    'update_json' => 'UpdateJson',
    'check_keys' => 'CheckKeys',
);

$command = "";
$args = array();

$oauth_base = "https://oauth.zettle.com";
$pusher_base = "https://pusher.izettle.com";
$subscriptions_url = $pusher_base . "/organizations/self/subscriptions";
$purchases_url = "https://purchase.izettle.com/purchases/v2";


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
        error_log("Calling " . $command);
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

    // $o['header'] = 'Authorization: Bearer '.$access_token.'\r\n' . 'Content-Type: application/json';
    $o['header'] = ["Authorization: Bearer " . $access_token, "Content-Type: application/json"];

    $opts = array('http' => $o);
    $context = stream_context_create($opts);
    $result = file_get_contents($url, false, $context);
    $jsonResult = json_decode($result);
    // $jsonResult['error'] = false;
    return $jsonResult;
}

function httpPost($url, $data, $headers, $auth = false, $json = false, $method = 'POST')
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
                // $headers[] = "Authorization: Bearer " . $login['access_token'];
                $headers['Authorization'] = "Bearer " . $login['access_token'];
            } else {
                // $headers[] = "Authorization: Bearer ".$_SESSION['access_token'];
                $headers['Authorization'] = "Bearer " . $_SESSION['access_token'];
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

    // Make up request options
    $options = [
        'headers' => $headers
    ];

    // custom_logs($options);

    if ($json) {
        $options['json'] = $data;
    } else {
        $options['form_params'] = $data;
    }

    $client = new Client();
    $response = $client->request($method, $url, $options);


    // $curl = curl_init($url);
    // curl_setopt($curl, CURLOPT_POST, true);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, $json ? json_encode($data) : http_build_query($data));
    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // $response = curl_exec($curl);
    // curl_close($curl);
    return json_decode($response->getBody());
}

// /plugin.php?plugin=fpp-zettle&page=zettle.php&command=login&nopage=1
function LoginUser()
{
    global $oauth_base;

    $pluginJson = convertAndGetSettings('zettle');

    $query = httpPost(
        $oauth_base . '/token',
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
        return [
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
    $complete_destination_url = explode_destination_url($destination_url, $settings);

    $query = httpPost(
        $subscriptions_url,
        [
            'uuid' => $_POST['uuid'],
            "transportName" => "WEBHOOK",
            "eventNames" => ["PurchaseCreated"],
            "destination" => 'https://' . $complete_destination_url,
            "contactEmail" => $_POST['contactEmail']
        ],
        [
            "Content-type" => "application/json"
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

        UpdateJson2('subscription', [
            'subscriptions' => $query,
            'organizationUuid' => $_POST['organizationUuid'],
        ]);

        echo json_encode([
            'error' => false,
            'message' => 'Purchase Subscription Created',
            //'subscription' => $query,
            //'organizationUuid' => $_POST['organizationUuid']
        ]);
    }
}

//plugin.php?plugin=fpp-zettle&page=zettle.php&command=update_subscription&nopage=1
function updatePurchaseSubscription()
{
    global $pusher_base;
    global $settings;

    $organization_uuid = $_POST['organization_uuid'];
    $subscription_uuid = $_POST['subscription_uuid'];

    // Put together url
    $url = $pusher_base . '/organizations/' . $organization_uuid . '/subscriptions/' . $subscription_uuid;
    // Get destination from form
    $destination_url = $_POST['destination'];
    // Explode destination_url to get parts that we need
    $complete_destination_url = explode_destination_url($destination_url, $settings);

    $query = httpPost(
        $url,
        [
            // 'uuid' => $_POST['uuid'],
            // "transportName"=> "WEBHOOK",
            "eventNames" => ["PurchaseCreated"],
            "destination" => 'https://' . $complete_destination_url,
            "contactEmail" => $_POST['contactEmail']
        ],
        [
            "Content-type: application/json"
        ],
        true,
        true,
        'PUT'
    );

    // Convert stdClass object to array
    $data = json_decode(json_encode($query), true);

    $pluginJson = convertAndGetSettings('zettle');

    $subscriptionData = $pluginJson['subscriptions'];

    $subscriptionData['destination'] = 'https://' . $complete_destination_url;
    $subscriptionData['contactEmail'] = $_POST['contactEmail'];

    updateJson2('update_subscription', [
        'subscriptions' => $subscriptionData
    ]);

    echo jsonOutput([
        'error' => false,
        'message' => 'Purchase Subscription Updated',
        // 'subscription' => $subscriptionData,
        // 'organizationUuid' => $organization_uuid
    ]);
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
        ], $oauth_base . '/users/self', true);

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
    $url = 'http://192.168.1.156/api/command/' . urlencode('Overlay Model Effect');

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
        "Thank You " . $_GET['name']
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

function GetPurchases()
{
    global $purchases_url;

    $current_year = date('Y');
    $current_month = date('m');

    $option = $_GET['option'];

    switch ($option) {
        case 'today':
            $data = [
                'startDate' => date('Y-m-d') . 'T00:00',
                'endDate' => date('Y-m-d') . 'T23:59'
            ];
            break;

        case 'yesterday':
            $data = [
                'startDate' => date('Y-m-d', strtotime("-1 days")) . 'T00:00',
                'endDate' => date('Y-m-d', strtotime("-1 days")) . 'T23:59',
            ];
            break;

        case 'this_week':
            $data = [
                'startDate' => date("Y-m-d", strtotime('monday this week')) . 'T00:00',
                'endDate' => date("Y-m-d", strtotime('sunday this week')) . 'T23:59'
            ];
            break;

        case 'this_month':
            $data = [
                'startDate' => implode('-', [
                    $current_year,
                    $current_month,
                    '01'
                ]),
                'endDate' => implode('-', [
                    $current_year,
                    $current_month,
                    cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year)
                ])
            ];
            break;

        default:
            $data = [];
    }

    $url = $purchases_url;
    if (!empty($data)) {
        $url .= '?' . http_build_query($data);
    }

    $query = buildQuery([], [
        'method'  => 'GET'
    ], $url);

    // if (isset($query['code'])) {
    //     echo json_encode([
    //         'error' => true,
    //         'message' => $query['message'] . '//Query Error'
    //     ]);
    // } else {
    $purchases = $query->purchases;

    $total = 0;
    foreach ($purchases as $purchase) {
        $total += $purchase->amount / 100;
    }

    echo '£' . number_format($total, 2);
    // }
}

function SavePushOver()
{
    UpdateJson2('pushover', [
        'activate' => $_POST['activate'],
        'app_token' => $_POST['app_token'],
        'user_key' => $_POST['user_key'],
        'message' => $_POST['message'],
    ]);

    echo jsonOutput([
        'error' => false,
        'message' => 'Pushover Updated!'
    ]);
}

function SavePublish()
{
    UpdateJson2('publish', [
        'activate' => $_POST['activate'],
        //'location' => $_POST['location'],
    ]);

    echo jsonOutput([
        'error' => false,
        'message' => 'Publish Updated!'
    ]);
}

function UpdateJson()
{
    $pluginJson = convertAndGetSettings('zettle');

    switch ($_POST['option']) {
        case 'setup':
            unset($_POST['option']);
            $pluginJson = array_merge($pluginJson, $_POST);
            break;

        case 'effect':
            unset($_POST['option']);

            $pluginJson = array_merge($pluginJson, $_POST);
            if ($pluginJson['multisyncCommand'] == "false") {
                $pluginJson['multisyncCommand'] = false;
            } else {
                $pluginJson['multisyncCommand'] = true;
            }
            break;
    }

    setPluginJSON('fpp-zettle', $pluginJson);

    echo json_encode([
        'error' => false
    ]);
}

function UpdateJson2($option, $data)
{
    $pluginJson = convertAndGetSettings('zettle');

    switch ($option) {
        case 'pushover':
            $pluginJson['pushover']['activate'] = $data['activate'];
            $pluginJson['pushover']['app_token'] = $data['app_token'];
            $pluginJson['pushover']['user_key'] = $data['user_key'];
            $pluginJson['pushover']['message'] = $data['message'];
            break;

        case 'subscription':
        case 'update_subscription';
            $pluginJson = array_merge($pluginJson, $data);
            break;

        case 'publish':
            $pluginJson['publish']['activate'] = $data['activate'];
            //$pluginJson['publish']['location'] = $data['location'];
    }

    setPluginJSON('fpp-zettle', $pluginJson);

    return true;
}

function CheckKeys($data = [])
{
    global $oauth_base;

    $query = httpPost(
        $oauth_base . '/token',
        [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'client_id' => $data['client_id'],
            'assertion' => $data['client_secret']
        ],
        [
            'Content-Type: application/x-www-form-urlencoded'
        ]
    );

    if (isset($query->error) || isset($query->code)) {
        return [
            'error' => true,
            'message' => isset($query->error) ? $query->error_description : $query->message
        ];
    }

    return [
        'error' => false,
        'message' => 'Key Valid'
    ];
}
