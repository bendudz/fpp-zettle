<link rel="stylesheet" href="/plugin.php?plugin=fpp-zettle&page=zettle.css&nopage=1">
<script type="text/javascript" src="/plugin.php?plugin=fpp-zettle&page=multiple-readers.js&nopage=1"></script>

<div id="global" class="settings">
  <template class="configRowTemplate">
    <div class="col-md-6 reader">
      <div class="card mb-4 box-shadow">
        <div class="card-header readerHeader">Reader</div>
        <div class="card-body">
          <div class="buttonCommandWrap mb-2">
            <div class="bb_commandTableWrap">
              <div class="bb_commandTableCrop">
                <table border=0 id="tableReaderTPL" class="tableReader">
                  <tr>
                    <td>Product Name:</td>
                    <td>
                      <input type="text" class="form-control readerProduct" placeholder="Product Name as on Zettle" required>
                    </td>
                  </tr>
                  <tr>
                    <td>Command:</td>
                    <td>
                      <select id="reader_TPL_Command" class="readerCommand" required>
                        <option value="" disabled selected>Select a Command</option>
                      </select>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="btn-group">
              <button type="button" class="btn btn-sm btn-outline-dark readerTest">Test</button>
              <button type="button" class="btn btn-sm btn-outline-danger readerDelete">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <div class="d-flex justify-content-between mb-1">
    <button id="addNewReader" class="buttons btn-success">
      <i class="fas fa-plus"></i> Add a Reader
    </button>
    <a href="plugin.php?_menu=content&plugin=fpp-zettle&page=setup.php" class="buttons">Back To Setup</a>
    <button id="saveReaderConfigButton" class="buttons btn-success">
      Save Readers
    </button>
  </div>
  <legend>Multiple Reader Config</legend>
  <p>Have more then one reader</p>
  <div class="alert alert-info">
    A product needs to be set per reader so it can be identified when a donation comes in.
  </div>
  <div class="readerList row"></div>
</div>
