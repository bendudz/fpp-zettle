#!/usr/bin/env php
<?php
$skipJSsettings=true;

include_once "/opt/fpp/www/config.php";
include_once "/opt/fpp/www/common.php";

include_once "/home/fpp/media/plugins/fpp-zettle/zettle.common.php";

$message = $argv[1];

$config = convertAndGetSettings('zettle');

// Check an command has set
if ($config['command'] != '') {
    // Build command url from selected command on setup page
    $url = 'http://127.0.0.1/api/command/'.urlencode($config['command']);
    // Get command args
    $data = $config['args'];
    // Check if command is "Overlay Model Effect"
        if ($config['command'] == 'Overlay Model Effect') {
            // Find and replace vaules in array as payment details
            $text = str_replace([
                '{{EVERYTHING}}',
                '{{TODAY}}',
                '{{THIS_MONTH}}'
            ], [
                runningTotal('everything'),
                runningTotal('today'),
                runningTotal('this_month')
            ], $message);
            // Remove and replace last item from array
            array_pop($data);
            $data[] = $text;
            // custom_logs($url);
            // custom_logs($data);

            // Fire the command
            $query = json_encode($data);
            $ch    = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_exec($ch);
            curl_close($ch);
        } else {
            custom_logs('ZettleTotals: Different command set run nothing');
        }
} else {
    custom_logs('ZettleTotals: No command set in setup page');
}

// Write to log file
custom_logs('command fired: ZettleTotals');
?>
