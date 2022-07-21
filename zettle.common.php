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
