<?php
include_once 'zettle.common.php';
$pluginName = 'zettle';

$pluginJson = convertAndGetSettings($pluginName);
$setDestination = $pluginJson['subscriptions']['destination'];
?>
<h2>Subscriptions</h2>
<div id="subscriptions"></div>
<script>
  $(function() {
    var setDestination = "<?php echo $setDestination; ?>";
    $.ajax({
      type: "GET",
      url: "plugin.php?plugin=fpp-zettle&page=zettle.php&command=subscriptions&nopage=1",
      dataType: "json",
      beforeSend: function() {
        $("#subscriptions").html('Loading subscriptions');
      },
      success: function(data) {
        $("#subscriptions").empty();
        if (data.subscriptions.length > 0 && !data.error) {
          $.each(data.subscriptions, function(key, val) {
            if (setDestination == val.destination) {
              $("#subscriptions").append("<div>" + val.uuid + " - " + val.destination + "</div>");
            } else {
              $("#subscriptions").append("<div>" + val.uuid + " - " + val.destination + " - <a href=''>Delete</a></div>");
            }
          });
        } else {
          $("#subscriptions").html('No subscriptions found');
        }
      }
    });
  });
</script>
