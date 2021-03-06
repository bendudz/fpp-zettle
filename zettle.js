var zettleConfig=null;
var uuidv1=null;

function uuid() {
  $.get('https://www.uuidtools.com/api/generate/v1', function (data) {
    uuidv1 = data[0];
  });
}

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

function InstallDone() {
  $('#closeDialogButton').show();
}

function CloseInstallDialog() {
  $('#installPopup').fppDialog('close');
  location.reload();
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
  // Genarate UUID
  uuid();

  $('#setup').on('submit', function(e) {
    e.preventDefault();

    var client_id = $("#client_id").val();
    var client_secret = $("#client_secret").val();

    SaveZettleConfig({
      "client_id": client_id,
      "client_secret": client_secret,
      "organizationUuid": zettleConfig.organizationUuid,
      "subscriptions": zettleConfig.subscriptions,
      "effect": zettleConfig.effect,
      "command": zettleConfig.command
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

  $('#clear_subscription').on('click', function(e) {
    if (confirm('CLEAR SUBSCRIPTION are you sure?')) {
      $.ajax({
        type: "GET",
        url: 'plugin.php?plugin=fpp-zettle&page=zettle.php&command=clear_subscription&nopage=1',
        dataType: 'json',
        async: false,
        data: {},
        processData: false,
        contentType: 'application/json',
        success: function(data) {
          $.jGrowl(data.message, {
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
        var subscription_data = {
          uuid: uuidv1,
          organizationUuid: data.organizationUuid,
          destination: $('#destination').val(),
          contactEmail: $('#contactEmail').val()
        };
        if ($('#password').length) {
          subscription_data.password = $('#password').val();
        }
        $.ajax({
          type: "POST",
          url: 'plugin.php?plugin=fpp-zettle&page=zettle.php&command=create_subscription&nopage=1',
          dataType: 'json',
          async: false,
          data: subscription_data,
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

  if ($('#api_effect').length > 0) {
    // $.ajax({
    //   type: "GET",
    //   url: '/api/effects',
    //   dataType: 'json',
    //   ContentType: 'application/json',
    //   async: false,
    //   data: {},
    //   processData: false,
    //   success: function(data) {
    //     $.each(data, function (i, item) {
    //       $('#select_effect').append($('<option>', {
    //         value: item,
    //         text: item
    //       }));
    //     });
    //   }
    // });
    // Wait for zettleConfig to load
    setTimeout(function() {
      var newButtonRowCommand = 'button_TPL_Command';
      var newButtonRowTable = 'tableButtonTPL';

      LoadCommandList(newButtonRowCommand);
      PopulateExistingCommand(zettleConfig, newButtonRowCommand,  newButtonRowTable, true);

      if (zettleConfig.command == 'Overlay Model Effect' && zettleConfig.args[2] == 'Text') {
        $('#text_options').show();
      }

      $('.buttonCommand').attr('id',newButtonRowCommand).on('change',function(){
        CommandSelectChanged(newButtonRowCommand, newButtonRowTable, true);
      });

      $('#tableButtonTPL_arg_3').on('change', function() {
        if ($(this).val() == 'Text') {
          $('#text_options').show();
        } else {
          $('#text_options').hide();
        }
      });

      // $('#select_effect option[value="' + zettleConfig.effect + '"]').attr('selected','selected');
    }, 1000);
  }

  $('#api_effect').on('submit', function(e) {
    e.preventDefault();

    var effect = $('#select_effect option:selected').val();

    $('[id^="tableButton"]').each(function(){
        var oldId = $(this).prop('id')
        var idArr = oldId.split('_');
        idArr[0]='tableButtonTPL'
        $(this).attr('id',idArr.join('_'));
        console.log($(this).attr('id',idArr.join('_')));
    });

    var zettle = {
      "client_id": zettleConfig.client_id,
      "client_secret": zettleConfig.client_secret,
      "organizationUuid": zettleConfig.organizationUuid,
      "subscriptions": zettleConfig.subscriptions
    };
    CommandToJSON('button_TPL_Command', 'tableButtonTPL', zettle);
    SaveZettleConfig(zettle, '#effect_save', true, 'Effect Saved!');
  });
  // Test command with out the need to save it first
  $('#test_command').on('click', function () {
    // Check for command
    if (zettleConfig.command == '') {
      // Display error to user if command not found
      DialogError('Error', 'No command found, please select a command!');
    } else {
      var url = "api/command/";
      var data = {};
      // Get command data
      CommandToJSON('button_TPL_Command', 'tableButtonTPL', data);
      // Build url with selected command
      url += data['command'];
      // Send ajax to test command to see if user likes it before they save it
      $.ajax({
        type: "POST",
        url: url,
        dataType: 'text',
        async: false,
        data: JSON.stringify(data['args']),
        processData: false,
        contentType: 'application/json',
        success: function (data) {
          if (data != '') {
            $.jGrowl('Test Sent!', { themeState: 'success' });
            $.jGrowl('If you like what you see don\'t forget to save it!!', { themeState: 'success', life: 5000 });
          }
        }
      });
    }
  });

  $('#install').on('submit', function(e) {
    e.preventDefault();

    $('#closeDialogButton').hide();
    $('#installPopup').fppDialog({ height: 600, width: 900, title: "Install Dataplicity", dialogClass: 'no-close' });
    $('#installPopup').fppDialog( "moveToTop" );
    $('#installText').html('');

    var command = $('#command').val();

    StreamURL('plugin.php?plugin=fpp-zettle&page=install-dataplicity.php&nopage=1&command=' + command , 'installText', 'InstallDone');
  });

  $('#api_effect').on('submit', function(e) {
    e.preventDefault();

    var effect = $('#select_effect option:selected').val();

    $('[id^="tableButton"]').each(function(){
        var oldId = $(this).prop('id')
        var idArr = oldId.split('_');
        idArr[0]='tableButtonTPL'
        $(this).attr('id',idArr.join('_'));
        console.log($(this).attr('id',idArr.join('_')));
    });

    var zettle = {
      "client_id": zettleConfig.client_id,
      "client_secret": zettleConfig.client_secret,
      "organizationUuid": zettleConfig.organizationUuid,
      "subscriptions": zettleConfig.subscriptions
    };
    CommandToJSON('button_TPL_Command', 'tableButtonTPL', zettle);
    SaveZettleConfig(zettle, '#effect_save', true, 'Effect Saved!');
  });
  // Test command with out the need to save it first
  $('#real_test').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      type: "GET",
      url: 'plugin.php?plugin=fpp-zettle&page=zettle.php&command=real_test&nopage=1',
      dataType: 'json',
      async: false,
      data: {
        name: $(this).find('#name').val(),
        amount: $(this).find('#amount').val()
      },
      processData: false,
      contentType: 'application/json',
      success: function(data) {
      }
    });
  });
});
