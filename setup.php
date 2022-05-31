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
    $j = "{\"client_id\": \"\", \"client_secret\": \"\", \"organizationUuid\": \"\", \"subscriptions\": [] }";
    return json_decode($j, true);
}

$pluginJson = convertAndGetSettings();
?>
<div id="global" class="settings">
    <legend>Zettle Setup</legend>
    <p>Add your client id and secret generated from the Zettle Integrations
        webpage</p>
    <script>
        $(function() {
            var zettleConfigJsonData = '<?php echo json_encode($pluginJson); ?>';
            var zettleConfigData = JSON.parse(zettleConfigJsonData);

            $('#setup').on('submit', function(e) {
                e.preventDefault();

                var client_id = $("#client_id").val();
                var client_secret = $("#client_secret").val();

                var zettleConfig = {
                    "client_id": client_id,
                    "client_secret": client_secret,
                    "organizationUuid": zettleConfigData.organizationUuid,
                    "subscriptions": zettleConfigData.subscriptions
                };

                $.ajax({
                    type: "POST",
                    url: 'fppjson.php?command=setPluginJSON&plugin=fpp-zettle',
                    dataType: 'json',
                    async: false,
                    data: JSON.stringify(zettleConfig),
                    processData: false,
                    contentType: 'application/json',
                    beforeSend: function() {
                        $('#save').prop('disabled', true);
                    },
                    success: function(data) {
                        $.jGrowl('Details saved', {
                            themeState: 'success'
                        });
                        setTimeout(function() {
                            //window.location.href = "plugin.php?_menu=content&plugin=fpp-zettle&page=create-subscription.php";
                            location.reload();
                        }, 3000);
                    },
                    error: function() {
                        $('#save').prop('disabled', false);
                        $.jGrowl('There was an error in saving your details!', {
                            themeState: 'danger'
                        });
                    }
                });
            });
        });

        function gotoCreateSubscriptions() {
            window.location.href =
                "plugin.php?_menu=content&plugin=fpp-zettle&page=create-subscription.php";
        }
    </script>
    <div class="row">
        <form id="setup" action="" method="post">
            <div class="col-auto mr-auto">
                <div class="row">
                    <div class="col-auto">
                        Client Id: &nbsp;<input type='text' id='client_id'
                            name='client_id'
                            value='<?php echo $pluginJson["client_id"] ?>'
                            required></input>
                    </div>
                    <div class="col-auto">
                        Client Secret: &nbsp;<input type='password'
                            id='client_secret' name='client_secret'
                            value='<?php echo $pluginJson["client_secret"] ?>'
                            required></input>
                    </div>
                </div>
                <div class="row">
                    <div class="col-auto">
                        <input id="save" type="submit" value="Save"
                            class="buttons btn-success"">
                        <?php if ($pluginJson['client_id'] != '' && count($pluginJson['subscriptions']) == 0) { ?>
                        <input type=" button" value="Create Subscription"
                            class="buttons"
                            onclick="gotoCreateSubscriptions();">
                        <?php } ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
