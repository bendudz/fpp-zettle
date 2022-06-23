<?
$skipJSsettings = 1;
require_once("common.php");

  DisableOutputBuffering();
  echo "Install Dataplicity has started" . "\n";
  $command = $_GET['command'] . " 2>&1";
	echo "Command: $command\n";
	echo "----------------------------------------------------------------------------------\n";
  system($command);
	echo "\n";
  echo "----------------------------------------------------------------------------------\n";
  echo "Dataplicity install complete.\n";
?>
