<?php
include_once 'zettle.common.php';
$pluginName = 'zettle';

$pluginJson = convertAndGetSettings($pluginName);

if (count($pluginJson['subscriptions']) > 0) {
    echo '<p class="mb-0">Subscription has been set up nothing to do here. Go to <a href="plugin.php?_menu=status&plugin=fpp-' . $pluginName . '&page=status.php">status page</a></p>';
} else { ?>
<script>
  $(function() {
    var zettleConfigJsonData = '<?php echo json_encode($pluginJson); ?>';
    var zettleConfigData = JSON.parse(zettleConfigJsonData);
    var pluginName = '<?php echo $pluginName; ?>';

    $('#subscription').on('submit', function(e) {
      e.preventDefault();

      $.ajax({
        type: "GET",
        url: 'plugin.php?plugin=fpp-' + pluginName + '&page=zettle.php&command=get_org_id&nopage=1',
        dataType: 'json',
        async: false,
        data: {},
        processData: false,
        contentType: 'application/json',
        success: function(data) {
          $.ajax({
            type: "POST",
            url: 'plugin.php?plugin=fpp-' + pluginName + '&page=zettle.php&command=create_subscription&nopage=1',
            dataType: 'json',
            async: false,
            data: {
              organizationUuid: data.organizationUuid,
              destination: $('#destination').val(),
              contactEmail: $('#contactEmail').val()
            },
            success: function(data) {
              if (data.error) {
                $.jGrowl('Error: ' + data.message, {
                  themeState: 'danger'
                });
              } else {
                $.jGrowl(data.message, {
                  themeState: 'success'
                });
                updateJson({
                  "client_id": zettleConfigData.client_id,
                  "client_secret": zettleConfigData.client_secret,
                  "organizationUuid": data
                    .organizationUuid,
                  "subscriptions": data.subscription
                }, 'Subscription Details Saved!');

                setTimeout(function() {
                  location.reload();
                }, 3000);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              DialogError('create_subscription Error', "ERROR: Error Please Try Again");
            }
          });
        },
        error: function(xhr, ajaxOptions, thrownError) {
          DialogError('get_org_id Error', "ERROR: There was an error getting get_org_id");
        }
      });
    });

    function updateJson(data, message) {
      $.ajax({
        type: "POST",
        url: 'fppjson.php?command=setPluginJSON&plugin=fpp-' + pluginName,
        dataType: 'json',
        async: false,
        data: JSON.stringify(data),
        processData: false,
        contentType: 'application/json',
        success: function(data) {
          $.jGrowl(message, {
            themeState: 'success'
          });
        }
      });
    }

    $('#status').on('click', function() {
      window.location.href = "plugin.php?_menu=status&plugin=fpp-" + pluginName + "&page=status.php";
    });
  });
</script>


<div id="global" class="settings">
  <legend>Create Subscription</legend>
    <div class="callout callout-warning">
      <h4>Warning:</h4>
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
          <input type='text' id='destination' value="<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/api/plugin/fpp-zettle/event'; ?>" required>
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
