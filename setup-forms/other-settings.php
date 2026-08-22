<legend>Other Settings</legend>
<form action="" id="other">
    <div class="container-fluid settingsTable settingsGroupTable">
        <div class="row">
            <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
                <div class="description">
                    <i class="fas fa-fw fa-nbsp ui-level-0"></i>Currency
                </div>
            </div>
            <div class="printSettingFieldCol col-md">
                <select id="other_currency" required class="form-control">
                    <option value="GBP" <?php echo $pluginJson['other']['currency'] == 'GBP' ? 'selected' : null; ?>>
                        GBP £</option>
                    <option value="USD" <?php echo $pluginJson['other']['currency'] == 'USD' ? 'selected' : null; ?>>
                        USD $</option>
                    <option value="EUR" <?php echo $pluginJson['other']['currency'] == 'EUR' ? 'selected' : null; ?>>
                        EUR €</option>
                </select>
            </div>
        </div>
    </div>
    <input type="submit" value="Save" class="buttons btn-success">
</form>