<legend>Publish</legend>
<p>We would like to gather some information from your donations. We would like to collect the donation amount and
    store it remotely on the <a href="https://fpp-zettle.co.uk" target="_blank">fpp-zettle.co.uk</a> server.</p>
<p>If you would like to take part in this please select "Yes" from the activate select and press save.</p>
<form action="" id="publish">
    <div class="container-fluid settingsTable settingsGroupTable">
        <div class="row">
            <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                <div class="description">
                    <i class="fas fa-fw fa-nbsp ui-level-0"></i>Activate
                </div>
            </div>
            <div class="printSettingFieldCol col-md">
                <select id="publish_activate" required class="form-control">
                    <option value="yes" <?php echo $pluginJson['publish']['activate'] == 'yes' ? 'selected' : null; ?>>Yes</option>
                    <option value="no" <?php echo $pluginJson['publish']['activate'] == 'no' ? 'selected' : null;
                                        echo !isset($pluginJson['publish']['activate']) ? 'selected' : ''; ?>>No</option>
                </select>
            </div>
        </div>
        <!-- <div class="row">
                <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                    <div class="description">
                        <i class="fas fa-fw fa-nbsp ui-level-0"></i>Location
                    </div>
                </div>
                <div class="printSettingFieldCol col-md">
                    <select id="publish_location" required class="form-control">
                        <option value="yes" <?php echo $pluginJson['publish']['location'] == 'yes' ? 'selected' : null; ?>>Yes</option>
                        <option value="no" <?php echo $pluginJson['publish']['location'] == 'no' ? 'selected' : null; ?>>No</option>
                    </select>
                </div>
            </div> -->
    </div>
    <input type="submit" value="Save" class="buttons btn-success">
</form>