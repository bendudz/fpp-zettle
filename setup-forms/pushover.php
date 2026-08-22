<legend>Pushover</legend>
<p>Get notification sent your phone every time a donate is made. Pushover is free to use for 30 days. If you want to
    use it for longer there is a $5 USD one-time purchase fee. Check out the details at there website: <a
        href="https://pushover.net/" target="_blank">https://pushover.net</a></p>

<form id="pushover" action="">
    <div class="container-fluid settingsTable settingsGroupTable">
        <div class="row">
            <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                <div class="description">
                    <i class="fas fa-fw fa-nbsp ui-level-0"></i>Activate
                </div>
            </div>
            <div class="printSettingFieldCol col-md">
                <select id="pushover_activate" required class="form-control">
                    <option value="yes" <?php echo $pluginJson['pushover']['activate'] == 'yes' ? 'selected' : null; ?>>Yes</option>
                    <option value="no" <?php echo $pluginJson['pushover']['activate'] == 'no' ? 'selected' : null;
                                        echo !isset($pluginJson['pushover']['activate']) ? 'selected' : ''; ?>>No</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                <div class="description">
                    <i class="fas fa-fw fa-nbsp ui-level-0"></i>Application API Token
                </div>
            </div>
            <div class="printSettingFieldCol col-md">
                <input type="text" id="pushover_app_token"
                    value="<?php echo $pluginJson["pushover"]["app_token"]; ?>" required class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                <div class="description">
                    <i class="fas fa-fw fa-nbsp ui-level-0"></i>User Key
                </div>
            </div>
            <div class="printSettingFieldCol col-md">
                <input type="text" id="pushover_user_key" value="<?php echo $pluginJson["pushover"]["user_key"]; ?>"
                    required class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                <div class="description">
                    <i class="fas fa-fw fa-nbsp ui-level-0"></i>Message
                </div>
            </div>
            <div class="printSettingFieldCol col-md">
                <input type="text" id="pushover_message" value="<?php echo $pluginJson["pushover"]["message"]; ?>"
                    required class="form-control">
            </div>
        </div>
    </div>
    <input type="submit" value="Save" class="buttons btn-success">
</form>