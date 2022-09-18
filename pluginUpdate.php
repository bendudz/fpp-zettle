<div id="pluginupdate" class="text-center" style="display: none;">
        <div class="updateTable text-success fppPluginEntryUpdateStatus">
            <i class="fas fa-exclamation-circle"></i> <b>Plugin Update Available</b>
        </div>
        <button class="buttons btn-success" onclick="UpgradePlugin();"><i class="far fa-arrow-alt-circle-down"></i> Update Now</button>
    </div>
<div id='pluginsProgressPopup' title='FPP Plugins' style="display: none">
    <textarea style='width: 100%;' rows="25"  disabled id='pluginsText'>
    </textarea>
    <input id='closeDialogButton' type='button' class='buttons' value='Close' onClick='ClosePluginProgressDialog();' style='display: none;'>
</div>
