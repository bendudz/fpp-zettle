<?php

include_once 'zettle.common.php';
$pluginName = 'zettle';
$pluginJson = convertAndGetSettings($pluginName);
?>
<script type="text/javascript" src="/plugin.php?plugin=fpp-zettle&file=zettle.js&nopage=1"></script>
<div id="global" class="settings">
    <legend>Zettle Setup</legend>
    <p>Add your client id and secret generated from the Zettle Integrations
        webpage</p>
    <form id="setup" action="" method="post">
        <div class="container-fluid settingsTable settingsGroupTable">
            <div class="row">
                <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                    <div class="description">
                        <i class="fas fa-fw fa-nbsp ui-level-0"></i>Client ID
                    </div>
                </div>
                <div class="printSettingFieldCol col-md">
                    <input type='text' id='client_id' value="<?php echo $pluginJson["client_id"]; ?>" required class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                    <div class="description">
                        <i class="fas fa-fw fa-nbsp ui-level-0"></i>Client Secret
                    </div>
                </div>
                <div class="printSettingFieldCol col-md">
                    <input type='password' id='client_secret' value="<?php echo $pluginJson["client_secret"]; ?>" required class="form-control">
                </div>
            </div>
        </div>
        <input id="save" type="submit" value="Save" class="buttons btn-success">
        <?php if ($pluginJson['client_id'] != '' && count($pluginJson['subscriptions']) == 0) { ?>
        <input id="createSubscriptions" type="button" value="Create Subscription" class="buttons">
        <?php } ?>
        <?php if ($pluginJson['client_id'] != '' && count($pluginJson['subscriptions']) > 0) { ?>
        <input id="clear_config" type="button" class="buttons" value="Clear Config">
        <?php } ?>
    </form>
    <?php if ($pluginJson['client_id'] != '' && count($pluginJson['subscriptions']) > 0) { ?>
    <legend>Effect</legend>
    <!--p>The effect that will be run when a transaction comes in.</p-->
    <p>Select a command that you would like to run when a transaction comes in</p>

    <div id="text_options" class="callout callout-warning" style="display: none;">
        <h4>Overlay Model Effect Text Options</h4>
        <p>There are a number of options available.</p>
        <ul>
            <li>{{PAYER_NAME}} : Show the name of the person that has donated</li>
            <li>{{AMOUNT}} : Show the amount the person donated</li>
        </ul>
    </div>

    <form id="api_effect" action="" method="post">
        <div class="container-fluid settingsTable settingsGroupTable">
            <div class="row">
                <div class="buttonCommandWrap">
                    <select id='button_TPL_Command' class="buttonCommand" required><option value="" disabled selected>Select a Command</option></select>
                    <div class="bb_commandTableWrap">
                        <div class="bb_commandTableCrop">
                            <table border=0 id='tableButtonTPL' class="tableButton">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--div class="container-fluid settingsTable settingsGroupTable">
            <div class="row">
                <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                    <div class="description">
                        <i class="fas fa-fw fa-nbsp ui-level-0"></i>Select effect
                    </div>
                </div>
                <div class="printSettingFieldCol col-md">
                    <select name="select_effect" id="select_effect" class="form-control">
                        <option value="">Select Effect</option>
                    </select>
                </div>
            </div>
        </div-->
        <input id="effect_save" type="submit" value="Save" class="buttons btn-success">
        <input id="test_command" type="button" value="Test" class="buttons">
    </form>

    <?php } ?>
</div>
