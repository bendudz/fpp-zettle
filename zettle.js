var zettleConfig=null;

function SaveZettleConfig(config, button='', reload=false, success_msg='') {
  var data = JSON.stringify(config);
  $.ajax({
    type: "POST",
    url: 'fppjson.php?command=setPluginJSON&plugin=fpp-zettle',
    dataType: 'json',
    async: false,
    data: data,
    processData: false,
    contentType: 'application/json',
    beforeSend: function() {
      if (button != '') {
        $(button).prop('disabled', true);
      }
    },
    success: function (data) {
      $.jGrowl(success_msg, {
          themeState: 'success'
      });
      if (reload) {;
        setTimeout(function() { location.reload(); }, 3000);
      }
    },
    error: function() {
      if (button != '') {
        $(button).prop('disabled', false);
      }
      DialogError('Error', "ERROR: There was an error, please try again!");
      }
  });
}

$(function() {
  $.ajax({
    type: "GET",
    url: 'fppjson.php?command=getPluginJSON&plugin=fpp-zettle',
    dataType: 'json',
    contentType: 'application/json',
    success: function (data) {
      if (typeof data==="string") {
          zettleConfig = $.parseJSON(data);
      } else {
          zettleConfig = data;
      }
    }
  });

  $('#setup').on('submit', function(e) {
    e.preventDefault();

    var client_id = $("#client_id").val();
    var client_secret = $("#client_secret").val();

    SaveZettleConfig({
      "client_id": client_id,
      "client_secret": client_secret,
      "organizationUuid": zettleConfig.organizationUuid,
      "subscriptions": zettleConfig.subscriptions,
      "effect": zettleConfig.effect
    }, '#save', true, 'Details Saved!');
  });

  $('#createSubscriptions').on('click', function() {
    window.location.href = "plugin.php?_menu=content&plugin=fpp-zettle&page=create-subscription.php";
  });

  $('#clear_config').on('click', function(e) {
    if (confirm('CLEAR CONFIG are you sure?')) {
      $.ajax({
        type: "GET",
        url: 'plugin.php?plugin=fpp-zettle&page=zettle.php&command=clear_config&nopage=1',
        dataType: 'json',
        async: false,
        data: {},
        processData: false,
        contentType: 'application/json',
        success: function(data) {
          $.jGrowl('Config Cleared!', {
              themeState: 'success'
          });
          setTimeout(function() { location.reload(); }, 1000);
        }
      });
    }
  });

  $('#status').on('click', function() {
    window.location.href = "plugin.php?_menu=status&plugin=fpp-zettle&page=status.php";
  });

  $('#clear_transactions').on('click', function(e) {
    var transactions = [];

    $.ajax({
        type: "POST",
        url: 'fppjson.php?command=setPluginJSON&plugin=fpp-zettle-transactions',
        dataType: 'json',
        async: false,
        data: JSON.stringify(transactions),
        processData: false,
        contentType: 'application/json',
        success: function(data) {
            $.jGrowl('Transactions cleared', {
                themeState: 'success'
            });
            setTimeout(function() {
                location.reload();
            }, 3000);
        },
        error: function() {
            $('#save').prop('disabled', false);
            DialogError('Error', "ERROR: There was an error in saving your details, please try again!");
        }
    });
  });

  $('#subscription').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      type: "GET",
      url: 'plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_org_id&nopage=1',
      dataType: 'json',
      async: false,
      data: {},
      processData: false,
      contentType: 'application/json',
      success: function(data) {
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
              SaveZettleConfig({
                "client_id": zettleConfig.client_id,
                "client_secret": zettleConfig.client_secret,
                "organizationUuid": data
                  .organizationUuid,
                "subscriptions": data.subscription
              }, '', false, 'Subscription Details Saved!');
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

  $('#status').on('click', function() {
    window.location.href = "plugin.php?_menu=status&plugin=fpp-zettle&page=status.php";
  });

  if ($('#select_effect').length > 0) {
    $.ajax({
      type: "GET",
      url: '/api/sequence',
      dataType: 'json',
      ContentType: 'application/json',
      async: false,
      data: {},
      processData: false,
      success: function(data) {
        $.each(data, function (i, item) {
          $('#select_effect').append($('<option>', {
            value: item,
            text: item
          }));
        });
      }
    });
    // Wait for zettleConfig to load
    setTimeout(function() {
      $('#select_effect option[value="' + zettleConfig.effect + '"]').attr('selected','selected');
    }, 1000);
  }
});
