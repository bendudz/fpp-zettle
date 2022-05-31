<h2>Zettle Status</h2>
<?
function convertAndGetSettings()
{
global $settings;

$cfgFile = $settings['configDirectory'] . "/plugin.fpp-zettle.json";
if (file_exists($cfgFile)) {
    $j = file_get_contents($cfgFile);
    $json = json_decode($j, true);
    return $json;
}
$j = "{\"client_id\": \"\", \"client_secret\": \"\", \"organizationUuid\": \"\", \"subscriptions\": [] }";
return json_decode($j, true);
}

$pluginJson = convertAndGetSettings();
?>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
    overflow:hidden;padding:10px 5px;word-break:normal;}
.tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
    font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
.tg .tg-3xvn{font-family:inherit;font-weight:bold;text-align:center;vertical-align:top}
.tg .tg-0lax{text-align:left;vertical-align:top}
</style>
<table class="tg">
<thead>
<tr>
    <!-- colspan="2" -->
    <th class="tg-3xvn" colspan="2"></th>
    <th class="tg-3xvn" colspan="3">Subscriptions</th>
</tr>
</thead>
<tbody>
<tr>
    <td class="tg-3xvn">Client ID</td>
    <td class="tg-3xvn">Client Secret</td>
    <td class="tg-3xvn">Email</td>
    <td class="tg-3xvn">Destination</td>
    <td class="tg-3xvn">Status</td>
<tr>

    <?foreach($pluginJson['subscriptions'] as $result){
        echo '<tr>';
        echo '<td class="tg-0lax">'.$pluginJson['client_id'].'</td>';
        echo '<td class="tg-0lax">********</td>';
        echo '<td class="tg-0lax">'.$result['contactEmail'].'</td>';
        echo '<td class="tg-0lax">'.$result['destination'].'</td>';
        echo '<td class="tg-0lax">'.$result['status'].'</td>';
        echo '</tr>';
    }?>
</tbody>
</table>
