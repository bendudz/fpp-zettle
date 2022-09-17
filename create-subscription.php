<?php
include_once 'zettle.common.php';
$pluginName = 'zettle';

$pluginJson = convertAndGetSettings($pluginName);
$checkForUIPassword = checkForUIPassword();

if (count($pluginJson['subscriptions']) > 0) { ?>
    <p class="mb-0">Subscription has been set up nothing to do here. Go to <a href="plugin.php?_menu=status&plugin=fpp-<?php echo $pluginName; ?>&page=status.php">status page</a> or back to <a href="plugin.php?_menu=content&plugin=fpp-<?php echo $pluginName; ?>&page=setup.php">set up page</a> to add a trigger.</p>
<?php } else { ?>
<link rel="stylesheet" href="/plugin.php?plugin=fpp-zettle&file=zettle.css&nopage=1">
<script type="text/javascript" src="/plugin.php?plugin=fpp-zettle&file=zettle.js&nopage=1"></script>
<div id="global" class="settings">
  <legend>Create Subscription</legend>
    <?php
    if (!checkForDataplicity()) {
    ?>
      <div class="callout callout-danger">
        <h4>Dataplicity:</h4>
        <p>Dataplicity is not installed, please install dataplicity and active wormhole on the device.</p>
        <p><a href="https://www.dataplicity.com/" target="_blank">Go To Dataplicity</a></a></p>
        <p>You could follow Greg Macaree video on whole subject <a href="https://www.youtube.com/watch?v=7LeD3dz-uXU" target="_blank">Click here for video</a></p>
      </div>
      <p>Or install Dataplicity in the web browser</p>
      <form id="install" action="" method="post">
        <div class="container-fluid settingsTable settingsGroupTable">
          <div class="row">
            <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
              <div class="description">
                <i class="fas fa-fw fa-nbsp ui-level-0"></i>Dataplicity Install Command
              </div>
            </div>
            <div class="printSettingFieldCol col-md">
              <input type='text' id='command' class="form-control" required placeholder="curl -s https://www.dataplicity.com/*****.py | sudo python">
            </div>
          </div>
        </div>
        <input id="install" type="submit" value="Install" class="buttons btn-success">
      </form>
      <div id='installPopup' title='Dataplicity Install' style="display: none">
        <textarea style='width: 99%; height: 500px;' disabled id='installText'></textarea>
        <input id='closeDialogButton' type='button' class='buttons' value='Close' onClick='CloseInstallDialog();' style='display: none;'>
      </div>
    <?php
    }
    ?>
    <div class="callout callout-info">
      <h4>Destination:</h4>
      <p>Need to be accessible to the internet. Press F1 for help on this topic.</p>
      <p>If you are using Dataplicity. Make sure you <b>Active</b> the wormhole from there web portal. Overwise you will get an error.</p>
    </div>
    <form id="subscription" action="" method="post">
    <div class="container-fluid settingsTable settingsGroupTable">
      <div class="row">
        <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
          <div class="description">
            <i class="fas fa-fw fa-nbsp ui-level-0"></i>Destination
          </div>
        </div>
        <div class="printSettingFieldCol col-md">
          <input type='text' id='destination' value="" placeholder="Wormhole Address" required autofocus>
          <img id='HostName_img' title='This is the url that zettle will talk to' src='images/redesign/help-icon.svg' class='icon-help'>
          <span id='HostName_tip' class='tooltip' style='display: none'>This is the url that zettle will talk to</span>
        </div>
      </div>
      <div class="row">
        <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
          <div class="description">
            <i class="fas fa-fw fa-nbsp ui-level-0"></i>Contact Email
          </div>
        </div>
        <div class="printSettingFieldCol col-md">
          <input type='email' id='contactEmail' value="" required>
          <img id='contactEmail_img' title='Used if there is an error' src='images/redesign/help-icon.svg' class='icon-help'>
          <span id='contactEmail_tip' class='tooltip' style='display: none'>Used if there is an error</span>
        </div>
      </div>
    </div>
    <input id="save" type="submit" value="Save" class="buttons btn-success">
    <input id="status" type="button" value="Back To Status Page" class="buttons">
  </form>
</div>
<?php }
