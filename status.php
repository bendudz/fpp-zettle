<h2>Zettle Status</h2>
<?
include_once 'zettle.common.php';
$pluginName = 'zettle';

$pluginJson = convertAndGetSettings($pluginName);
$plugindatabase = convertAndGetSettings($pluginName . "-transactions");

$setupUrl = 'plugin.php?' . http_build_query([
        '_menu' => 'content',
        'plugin' => 'fpp-' . $pluginName,
        'page' => 'setup.php'
    ]);

function sortByTimestampDesc($a, $b)
{
    return $b['timestamp'] > $a['timestamp'];
}

function getTransactions()
{
    $data = convertAndGetSettings('zettle-transactions');
    usort($data, 'sortByTimestampDesc');
    return $data;
}

function getStatusData($pj)
{
    return [[
        "client_id" => $pj['client_id'],
        "client_secret" => substr($pj['client_secret'], 0, 8),
        "email" => $pj['subscriptions']['contactEmail'],
        "destination" => $pj['subscriptions']['destination'],
        "status" => $pj['subscriptions']['status']
    ]];
}

?>
<head>
    <link
            href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css"
            rel="stylesheet"
    />
    <style type="text/css">
        .hidetext {
            -webkit-text-security: disc; /* Default */
        }
    </style>
    <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>
</head>
<body>
<?php if ($pluginJson['client_id'] != '') { ?>
<div id="status"></div>
<script>new gridjs.Grid({
        columns: [{
            id: 'client_id',
            name: 'Client ID'
        }, {
            id: 'client_secret',
            name: 'Client Secret',
            formatter: (cell) => `******`
        }, {
            id: 'email',
            name: 'Email'
        }, {
            id: 'destination',
            name: 'Destination'
        }, {
            id: 'status',
            name: 'Status'
        }],
        resizable: true,
        style: {
            table: {
                border: '3px solid #ccc'
            },
            th: {
                'background-color': 'rgba(0, 0, 0, 0.1)',
                color: '#000',
                'border-bottom': '3px solid #ccc',
                'text-align': 'center'
            },
            td: {
                'text-align': 'center'
            }
        },
        data:
        <?
        echo json_encode(getStatusData($pluginJson));
        ?>

    }).render(document.getElementById("status"));
</script>
<br><br>
<h3>Transactions</h3>
<div id="transactions"></div>
<script>
    const grid = new gridjs.Grid({
        columns: [{
            id: 'timestamp',
            name: 'Time',
            width: '33%',
            formatter: (data) => {
                return new Date(data).toGMTString();
            }
        }, {
            id: 'amount',
            name: 'Amount £',
            width: '33%',
            formatter: (cell) => `£${cell}`
        }, {
            id: 'userDisplayName',
            name: 'User Display Name',
            width: '33%'
        }],
        sort: true,
        resizable: true,
        style: {
            table: {
                border: '3px solid #ccc'
            },
            th: {
                'background-color': 'rgba(0, 0, 0, 0.1)',
                color: '#000',
                'border-bottom': '3px solid #ccc',
                'text-align': 'center'
            },
            td: {
                'text-align': 'center'
            }
        },
        data:
        <?
        echo json_encode(getTransactions());
        ?>

    });
    grid.render(document.getElementById("transactions"));
</script>
</body>
<?php } else { ?>
    <p>You need to configure this plugin before you can see the status. Click here to get to <a
                href="<?php echo $setupUrl; ?>">setup</a></p>
<?php } ?>
