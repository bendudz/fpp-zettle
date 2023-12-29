var zettleConfig = null;

function SaveBigButtonConfig(config) {
  var data = JSON.stringify(config);
  $(".readerList").addClass("loading");
  $.ajax({
    type: "POST",
    url: 'api/configfile/plugin.fpp-zettle.json',
    dataType: 'json',
    async: false,
    data: data,
    processData: false,
    contentType: 'application/json',
    success: function (data) {
      $(".readerList").removeClass("loading");
      $.jGrowl('Reader Config Saved!', {
        themeState: 'success'
      });
    }
  });
}

function GetReader(i, v) {
  var reader = {
    "product": $('#reader-' + i + '_Product').val(),
  };
  CommandToJSON('reader-' + i + '_Command', 'tableReader-' + i, reader);
  return reader;
}

function SaveReaders() {
  // Reset readers to empty array
  zettleConfig["readers"] = [];
  // Look over readerList children
  $.each($('.readerList').children(), function (i, v) {
    var key = "" + i;
    var reader = GetReader(i, v);
    zettleConfig["readers"][key] = reader;
  });
  // console.log(zettleConfig);
  SaveBigButtonConfig(zettleConfig);
}

function updateReaderRow(i, v) {
  var $newReaderRow = $(v);
  var newReaderRowCommand = 'reader-' + i + '_Command';
  var newReaderRowProduct = 'reader-' + i + '_Product';
  var newReaderRowTable = 'tableReader-' + i;

  $newReaderRow.data('rKey', i);
  $newReaderRow.find('.buttonCommand').attr('id', newReaderRowCommand);
  $newReaderRow.find('.readerProduct').attr('id', newReaderRowProduct);
  $newReaderRow.find('.readerHeader').html('Reader ' + (i + 1));

  $newReaderRow.find('[id^="tableReader"]').each(function () {
    var oldId = $(this).prop('id')
    var idArr = oldId.split('_');
    idArr[0] = newReaderRowTable
    $(this).attr('id', idArr.join('_'))

  });
  return $newReaderRow;
}

function createReaderRow(i, v) {
  // console.log('createReaderRow');
  var $newReaderRow = $($(".configRowTemplate").html());
  var newReaderRowCommand = 'reader-' + i + '_Command';
  var newReaderRowProduct = 'reader-' + i + '_Product';
  var newReaderRowTable = 'tableReader-' + i;

  $newReaderRow.data('rKey', i);
  $newReaderRow.find('.readerCommand').attr('id', newReaderRowCommand).on('change', function () {
    CommandSelectChanged(newReaderRowCommand, newReaderRowTable, true);
  });

  $newReaderRow.find('.readerHeader').html('Reader ' + (i + 1));
  if (!v) {
    $newReaderRow.find('.readerProduct').attr('id', newReaderRowProduct).val();
  } else {
    $newReaderRow.find('.readerProduct').attr('id', newReaderRowProduct).val(v.product);
  }
  $newReaderRow.find('.tableReader').attr('id', newReaderRowTable);

  $newReaderRow.find('.readerDelete').click(function () {
    $(this).closest('.reader').remove();
    $.each($('.readerList').children(), function (iteration, value) {
      updateReaderRow(iteration, value);
    });
  });

  $newReaderRow.find('.readerTest').click(function () {
    var command = $(document).find('#' + newReaderRowCommand).val();
    if (command == null) {
      $.jGrowl('No command selected, please select a command!', {
        themeState: 'danger'
      });
    } else {
      var url = "api/command/";
      var data = {};
      // Get command data
      CommandToJSON(newReaderRowCommand, newReaderRowTable, data);
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
            $.jGrowl('Test Sent!', {
              themeState: 'success'
            });
            $.jGrowl('If you like what you see don\'t forget to save it!!', {
              themeState: 'success',
              life: 5000
            });
          }
        }
      });
    }
  });

  $('.readerList').append($newReaderRow);
  LoadCommandList('reader-' + i + '_Command');

  return $newReaderRow;
}

$(function () {
  // allowMultisyncCommands = true;

  $('#saveReaderConfigButton').click(function () {
    SaveReaders();
  });

  $(".readerList").addClass("loading");
  $.get('api/configfile/plugin.fpp-zettle.json')
    .done(function (data) {
      $(".readerList").removeClass("loading");
      processBigButtonConfig(data);
    })
    .fail(function (data) {
      $(".readerList").removeClass("loading");
      processBigButtonConfig('[]');
    });

  function processBigButtonConfig(data) {
    if (typeof data === "string") {
      zettleConfig = $.parseJSON(data);
    } else {
      zettleConfig = data;
    }

    // console.log(zettleConfig);

    if (zettleConfig.readers.length < 1) {
      zettleConfig.readers.push({
        "product": "",
        "command": "",
      })
    }

    $.each(zettleConfig.readers, function (i, v) {
      $newReaderRow = createReaderRow(i, v);
      PopulateExistingCommand(v, 'reader-' + i + '_Command', 'tableReader_' + i, true);
    });
  }

  $("#addNewReader").click(function () {
    var i = $(".readerList").children().length;
    var $newReaderRow = createReaderRow(i, null);
  });
});
