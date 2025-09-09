<?php
include 'vendor/autoload.php';
include_once 'zettle.common.php';
$pluginName = 'zettle';

$pluginJson = convertAndGetSettings($pluginName);
$setDestination = $pluginJson['subscriptions']['destination'];


$uuid = \AbmmHasan\Uuid::v1();
$subscription = [
  'destination' => $pluginJson['subscriptions']['destination'],
  'contact_email' => $pluginJson['subscriptions']['contactEmail'],
  'organization_uuid' => $pluginJson['organizationUuid'],
];
?>
<h2>Subscriptions</h2>
<div id="subscriptions"></div>

<div id="none" style="display: none">
  <p>You have subscriptions confired but zettle does not see it.</p>
  <a id="recreate" href="plugin.php?plugin=fpp-zettle&page=zettle.php&command=create_subscription&nopage=1">Recrate
    Subscription</a>
</div>

<script>
  $(function () {
    var setDestination = "<?php echo $setDestination; ?>";
    $.ajax({
      type: "GET",
      url: "plugin.php?plugin=fpp-zettle&page=zettle.php&command=subscriptions&nopage=1",
      dataType: "json",
      beforeSend: function () {
        $("#subscriptions").html('Loading subscriptions');
      },
      success: function (data) {
        $("#subscriptions").empty();
        if (data.subscriptions.length > 0 && !data.error) {
          $.each(data.subscriptions, function (key, val) {
            if (setDestination == val.destination) {
              $("#subscriptions").append("<div>" + val.uuid + " - " + val.destination + "</div>");
            } else {
              $("#subscriptions").append("<div>" + val.uuid + " - " + val.destination + " - <a href='test' id='delSub' data-subid='" + val.uuid + "'>Delete</a></div>");
            }
          });
        } else {
          $("#subscriptions").html('No subscriptions found');
          $("#none").show();
        }
      },
      error: function (xhr, ajaxOptions, thrownError) {
        $("#subscriptions").html('There was an error getting subscriptions');
      }
    });

    $('#recreate').on('click', function (e) {
      e.preventDefault();

      var subscription_data = {
        destination: "<?php echo $subscription['destination'] ?>",
        uuid: "<?php echo $uuid; ?>",
        contactEmail: "<?php echo $subscription['contact_email'] ?>",
        organizationUuid: "<?php echo $subscription['organization_uuid']; ?>",
      };

      $.ajax({
        type: "POST",
        url: $(this).attr('href'),
        dataType: 'json',
        async: false,
        data: subscription_data,
        success: function (data) {
          if (data.error) {
            $.jGrowl('Error: ' + data.message, {
              themeState: 'danger'
            });
          } else {
            $.jGrowl(data.message, {
              themeState: 'success'
            });
            setTimeout(function () {
              location.reload();
            }, 3000);
          }
        },
        error: function (xhr, ajaxOptions, thrownError) {
          DialogError('create_subscription Error', "ERROR: Error Please Try Again");
        }
      });
    });

    $(document).on('click', '#delSub', function (e) {
      e.preventDefault();

      var thisObj = $(this),
        thisSubId = thisObj.attr('data-subid');

      console.log(thisSubId);
    });
  });
</script>