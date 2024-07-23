<?php
include 'vendor/autoload.php';

use GuzzleHttp\Client;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$oauth_base = "https://oauth.zettle.com";
// LoginUser();

function authHeader()
{
  $access_token = LoginUser()['access_token'];

  return [
    'Authorization' => 'Bearer ' . $access_token,
  ];
}

function LoginUser()
{
  global $oauth_base;

  $guzzle = new Client();

  $pluginJson = [
    'client_id' => 'ff850f22-c155-11ee-a052-7beddfe3fb09',
    'client_secret' => 'eyJraWQiOiIwIiwidHlwIjoiSldUIiwiYWxnIjoiUlMyNTYifQ.eyJpc3MiOiJpWmV0dGxlIiwiYXVkIjoiQVBJIiwiZXhwIjoyNjUzNTM2MzM1LCJzdWIiOiI5YTFkN2QzYS1kZjYzLTExZWMtYjk4Ni02YjI2YTg4OTA3YjIiLCJpYXQiOjE3MDY4Mjg1NTksInJlbmV3ZWQiOmZhbHNlLCJjbGllbnRfaWQiOiJmZjg1MGYyMi1jMTU1LTExZWUtYTA1Mi03YmVkZGZlM2ZiMDkiLCJzY29wZSI6WyJSRUFEOkZJTkFOQ0UiLCJSRUFEOlBVUkNIQVNFIiwiUkVBRDpQUk9EVUNUIl0sInVzZXIiOnsidXNlclR5cGUiOiJVU0VSIiwidXVpZCI6IjlhMWQ3ZDNhLWRmNjMtMTFlYy1iOTg2LTZiMjZhODg5MDdiMiIsIm9yZ1V1aWQiOiI5YTFhN2I0ZS1kZjYzLTExZWMtOTM2ZC0zYjBmZWVlYWE2ODkiLCJ1c2VyUm9sZSI6Ik9XTkVSIn0sInR5cGUiOiJ1c2VyLWFzc2VydGlvbiJ9.REw6c0Lsm7ge7uA_bHWaMbX901iLh545z3jE4duqT7XHjk6wjHN0d8AWNSqw8r5uObRzy_s_AibEm0sBfp0pDhIwyaKUke9nwIFPWploT2FHiGpsMFDY-UfZk93SDr-vItvoeykuIT2ij53HX02XmE4BSPg4ZrSCNX2KtBsieXBuJGHwNdlDrqipUayDH0vbbvNgW2045ZUGwOiXqtxsoe_I9D0hgM-G8uFjoWXQR5tUqyO1nJFwR7Ks2IHKgUmsYYwJ5-duHYueSvb13ZL1IM9JPHb-ucfHmdnTB4eXjX7IOa5yI3a7YA2xU2D_E3D39ga6TGoyW-p1w14FHgsHdg',
  ];

  try {
    $request = $guzzle->post($oauth_base . '/token', [
      // 'headers' => [
      //   'Content-Type' => 'application/x-www-form-urlencoded'
      // ],
      'form_params' => [
        'client_id' => $pluginJson['client_id'],
        'assertion' => $pluginJson['client_secret'],
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
      ],
    ]);

    $response = json_decode($request->getBody()->getContents());
  } catch (GuzzleHttp\Exception\ClientException $e) {
    $response = json_decode($e->getResponse()->getBody()->getContents());
  }

  if (isset($response->error) || isset($response->code)) {
    return [
      'error' => true,
      'message' => isset($response->error) ? $response->error_description : $response->message
    ];
  }

  $access_token = $response->access_token;

  return [
    'error' => false,
    'access_token' => $access_token
  ];
}

function GetSubscriptions()
{
  $guzzle = new Client();
  $request = $guzzle->get('https://pusher.izettle.com/organizations/self/subscriptions', [
    'headers' => [
      'Authorization' => 'Bearer ' . LoginUser()['access_token'],
    ]
  ]);

  $response = json_decode($request->getBody()->getContents());
  return $response;
}

echo '<pre>' . print_r(GetSubscriptions(), true) . '</pre>';
