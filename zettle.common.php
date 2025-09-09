<?php

function emptyConfig()
{
  return [
    'client_id' => '',
    'client_secret' => '',
    'organizationUuid' => '',
    'subscriptions' => [],
    'effect_activate' => 'no',
    'command' => ''
  ];
}

function convertAndGetSettings($filename)
{
  global $settings;

  $cfgFile = $settings['configDirectory'] . "/plugin.fpp-" . $filename . ".json";
  if (file_exists($cfgFile)) {
    $j = file_get_contents($cfgFile);
    $json = json_decode($j, true);
    return $json;
  }
  // Create json for config not found
  if ($filename == 'zettle') {
    $j = json_encode(emptyConfig());
  }
  // Create json for transactions not found
  if ($filename == 'zettle-transactions') {
    $j = json_encode([]);
  }
  return json_decode($j, true);
}

function writeToJsonFile($filename, $data)
{
  global $settings;

  $cfgFile = $settings['configDirectory'] . "/plugin.fpp-zettle-" . $filename . ".json";
  $json_data = json_encode($data);
  file_put_contents($cfgFile, $json_data);
}

function readConfig()
{
  global $settings;

  $url = $settings['configDirectory'] . "/plugin.fpp-zettle.json";
  $config = file_get_contents($url);
  $config = utf8_encode($config);
  return json_decode($config);
}

function readTransactions()
{
  global $settings;

  $url = $settings['configDirectory'] . "/plugin.fpp-zettle-transactions.json";
  $config = file_get_contents($url);
  $config = utf8_encode($config);
  return json_decode($config);
}

function logEntry($data)
{
  global $settings;

  $logFile = $settings['logDirectory'] . "/fpp-zettle.log";
  $logWrite = fopen($logFile, "a") or die("Unable to open file!");
  fwrite($logWrite, date('Y-m-d h:i:s A', time()) . ": " . $data . "\n");
  fclose($logWrite);
}

function checkForDataplicity()
{
  $serial = '/opt/dataplicity/tuxtunnel/serial';
  return file_exists($serial);
}

function checkForUIPassword()
{
  global $settings;

  if (array_key_exists('passwordEnable', $settings)) {
    return $settings['passwordEnable'] == "0" ? false : true;
  }
  return false;
}

function outputSettings()
{
  global $settings;
  return $settings;
}

function custom_logs($message)
{
  global $settings;
  if (is_array($message)) {
    $message = json_encode($message);
  }
  $file = fopen($settings['logDirectory'] . "/fpp-zettle.log", "a");
  fwrite($file, "\n" . date('Y-m-d H:i:s') . " :: " . $message);
  fclose($file);
  return;
}

function totalTransactions($amount = 0)
{
  global $settings;

  $filepath = $settings['configDirectory'] . "/plugin.fpp-zettle-transactions-total.txt";
  // Check if exists
  if (!file_exists($filepath)) {
    file_put_contents($filepath, 0);
  }
  // Get current total
  $current = (int) file_get_contents($filepath);
  // Check if amount is set
  if ($amount > 0) {
    // Add amount to current total
    $newTotal = $current + $amount;
    // Write to file
    file_put_contents($filepath, $newTotal);
    return number_format($newTotal, 2);
  } else {
    return number_format($current, 2);
  }
}

function runningTotal($option = 'everything')
{
  return trim(file_get_contents('http://localhost/plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_purchases&nopage=1&option=' . $option));
}

function isSiteAvailible($url)
{
  // Check, if a valid url is provided
  if (!filter_var($url, FILTER_VALIDATE_URL)) {
    return false;
  }

  // Initialize cURL
  $curlInit = curl_init($url);

  // Set options
  curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($curlInit, CURLOPT_HEADER, true);
  curl_setopt($curlInit, CURLOPT_NOBODY, true);
  curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

  // Get response
  $response = curl_exec($curlInit);
  $httpcode = curl_getinfo($curlInit, CURLINFO_HTTP_CODE);

  // Close a cURL session
  curl_close($curlInit);

  // return $response?true:false;
  return $httpcode;
}

/**
 * Explode destination url make sure all is right
 *
 * @param string $url This is the url that zettle will talk to
 * @param array $settings fpp settings
 * @return string
 */
function explode_destination_url(string $url, array $settings)
{
  $explode_destination_url = explode('/', $url);
  // Get the url that we need
  $base_url = $explode_destination_url[2];
  // Check if explode_destination_url last item == event
  if (end($explode_destination_url) == 'event') {
    // Unset first two explode_destination_url items
    unset($explode_destination_url[0]);
    unset($explode_destination_url[1]);
    // Rebuild destination url
    return implode('/', $explode_destination_url);
  } else {
    // Build destination url with added part
    return $base_url . '/api/plugin/fpp-zettle/event';
  }
  // // Check for ui password
  // if (checkForUIPassword()) {
  //     // Build username_password
  //     $username_password = 'admin:' . $settings['password'] . '@';
  //     // Combine username_password and destination
  //     $complete_destination_url = $username_password . $destination;
  // } else {
  //     // set destination as password is not found
  //     $complete_destination_url = $destination;
  // }

  // return $complete_destination_url;
}

function jsonOutput($array)
{
  return json_encode($array);
}

function currencyWorkout($currency)
{
  switch ($currency) {
    case 'USD':
      $c = '$';
      break;

    case 'EUR':
      $c = '€';
      break;

    default:
      $c = '£';
  }

  return $c;
}