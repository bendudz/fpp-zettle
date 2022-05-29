<?php

$oauth_base = "https://oauth.zettle.com";
$subscriptions_url = "https://pusher.izettle.com/organizations/self/subscriptions";

function getToken()
{
}

function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function returnIfExists($json, $setting)
{
    if ($json == null) {
        return "";
    }
    if (array_key_exists($setting, $json)) {
        return $json[$setting];
    }
    return "";
}

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

$pluginJson = convertAndGetSettings();
?>
<div id="global" class="settings">
    <legend>Zettle Setup</legend>
    <p>Add your client id and secret generated from the Zettle Integrations webpage</p>
    <script>
        var
            zettleConfig = <?php echo json_encode($pluginJson, JSON_PRETTY_PRINT); ?> ;

        function SaveZETTLEConfig(config) {
            var data = JSON.stringify(config);
            $.ajax({
                type: "POST",
                url: 'fppjson.php?command=setPluginJSON&plugin=fpp-zettle',
                dataType: 'json',
                async: false,
                data: data,
                processData: false,
                contentType: 'application/json',
                success: function(data) {
                    console.log('saved');
                }
            });
        }

        function SaveZETTLE() {
            var client_id = $("#client_id").val();
            var client_secret = $("#client_secret").val();
            // TODO read in subs & orgid as to not rewrite over them?
            var zettleConfig = {
                "client_id": client_id,
                "client_secret": client_secret,
                "organizationUuid": "",
                "subscriptions": []
            };
            SaveZETTLEConfig(zettleConfig);
        }
    </script>
    <div class="row">
        <div class="col-auto mr-auto">
            <div class="row">
                <div class="col-auto">
                    Client Id: &nbsp;<input type='text' id='client_id'
                        value='<?php echo $pluginJson["client_id"] ?>'
                        required></input>
                </div>
                <div class="col-auto">
                    Client Secret: &nbsp;<input type='password'
                        id='client_secret'
                        value='<?php echo $pluginJson["client_secret"] ?>'
                        required></input>
                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <input type="button" value="Save"
                        class="buttons genericButton" onclick="SaveZETTLE();">
                </div>
            </div>
        </div>
    </div>
</div>
