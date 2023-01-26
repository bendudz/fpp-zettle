<?php
include_once 'zettle.common.php';
$pluginName = 'zettle';

$pluginJson = convertAndGetSettings($pluginName);

if (count($pluginJson['subscriptions']) == 0) { ?>
    <p class="mb-0">Subscription has been not been setup.</p>
    <a href="plugin.php?_menu=status&plugin=fpp-zettle&page=create-subscription.php" class="buttons">Create Subscription</a>
<?php } else { ?>
<link rel="stylesheet" href="/plugin.php?plugin=fpp-zettle&file=zettle.css&nopage=1">
<script type="text/javascript" src="/plugin.php?plugin=fpp-zettle&file=zettle.js&nopage=1"></script>
<div id="global" class="settings">
  <legend>Update Subscription</legend>
  <form id="subscription" action="" method="post">
    <input type="hidden" name="organization_uuid" id="organization_uuid" value="<?php echo $pluginJson['organizationUuid']; ?>">
    <input type="hidden" name="subscription_uuid" id="subscription_uuid" value="<?php echo $pluginJson['subscriptions'][0]['subscriptionUuid']; ?>">
    <div class="container-fluid settingsTable settingsGroupTable">
      <div class="row">
        <div class="printSettingLabelCol col-md-4 col-lg-3 col-xxxl-2">
          <div class="description">
            <i class="fas fa-fw fa-nbsp ui-level-0"></i>Destination
          </div>
        </div>
        <div class="printSettingFieldCol col-md">
          <input type='text' id='destination' value="<?php echo $pluginJson['subscriptions'][0]['destination'] ?>" placeholder="Wormhole Address" required autofocus style="width: 97%;">
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
          <input type='email' id='contactEmail' value="<?php echo $pluginJson['subscriptions'][0]['contactEmail'] ?>" required style="width: 97%;">
          <img id='contactEmail_img' title='Used if there is an error' src='images/redesign/help-icon.svg' class='icon-help'>
          <span id='contactEmail_tip' class='tooltip' style='display: none'>Used if there is an error</span>
        </div>
      </div>
    </div>
    <input id="save" type="submit" value="Save" class="buttons btn-success">
    <a href="plugin.php?_menu=status&plugin=fpp-zettle&page=status.php" class="buttons">Back to Status Page</a>
  </form>
</div>
<?php } ?>
