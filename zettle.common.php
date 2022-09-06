<?php

function emptyConfig()
{
  return [
    'client_id' => '',
    'client_secret'  => '',
    'organizationUuid' => '',
    'subscriptions' => [],
    'effect' => '',
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

function custom_logs($message) {
  global $settings;
  if( is_array($message) ) {
        $message = json_encode($message);
  }
  $file = fopen($settings['logDirectory'] . "/fpp-zettle.log","a");
  fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: " . $message);
  fclose($file);
	return;
}

function totalTransactions($amount = 0) {
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

function runningTotal($option = 'everything') {
    return file_get_contents('http://localhost/plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_purchases&nopage=1&option=' . $option);
}
