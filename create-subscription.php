<?php
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

if (count($pluginJson['subscriptions']) > 0) {
    echo 'Subscription has been set up nothing to do here';
} else { ?>
<script>
  var
    zettleConfig = <?php echo json_encode($pluginJson, JSON_PRETTY_PRINT); ?> ;

  function SaveSubscription() {
    var destination = $('#destination').val();
    var contactEmail = $('#contactEmail').val();

    $.ajax({
      type: "GET",
      url: 'plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_org_id&nopage=1',
      dataType: 'json',
      async: false,
      data: {},
      processData: false,
      contentType: 'application/json',
      success: function(data) {
        // updateJson({
        //   "client_id": zettleConfig.client_id,
        //   "client_secret": zettleConfig.client_secret,
        //   "organizationUuid": data.organizationUuid,
        //   "subscriptions": []
        // }, 'OrganizationUuid saved');

        $.ajax({
          type: "POST",
          url: 'plugin.php?plugin=fpp-zettle&page=zettle.php&command=create_subscription&nopage=1',
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
                "client_id": zettleConfig.client_id,
                "client_secret": zettleConfig.client_secret,
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
            $.jGrowl('Error Please Try Again', {
              themeState: 'danger'
            });
          }
        });
      },
      error: function(xhr, ajaxOptions, thrownError) {
        $.jGrowl('There was an error!', {
          themeState: 'danger'
        });
      }
    });
  }

  function updateJson(data, message) {
    $.ajax({
      type: "POST",
      url: 'fppjson.php?command=setPluginJSON&plugin=fpp-zettle',
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
</script>


<div id="global" class="settings">
  <legend>Create Subscription</legend>
  <div class="row">
    <div class="col-auto mr-auto">
      <div class="row">
        <div class="col-auto">
          Destination: &nbsp;<input type='text' id='destination'
            value="<?php echo 'https://'.$_SERVER['HTTP_HOST'].'/api/plugin/fpp-zettle/event'; ?>"
            required></input>
        </div>
        <div class="col-auto">
          Contact Email: &nbsp;<input type='email' id='contactEmail'
            value="test@test.com" required></input>
        </div>
      </div>
      <div class="row">
        <div class="col-auto">
          <input type="button" value="Save" class="buttons genericButton"
            onclick="SaveSubscription();">
        </div>
      </div>
    </div>
  </div>
</div>
<?php }
