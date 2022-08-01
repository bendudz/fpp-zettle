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

$subscriptionUrl = 'plugin.php?' . http_build_query([
        '_menu' => 'content',
        'plugin' => 'fpp-' . $pluginName,
        'page' => 'create-subscription.php'
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

function getTransactionsTotal()
{
    $transactions = convertAndGetSettings('zettle-transactions');
    $total = 0;
    foreach ($transactions as $transaction) {
        $total += $transaction['amount'];
    }
    return number_format($total, 2);
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
    <link rel="stylesheet" href="https://unpkg.com/gridjs/dist/theme/mermaid.min.css">
    <link rel="stylesheet" href="/plugin.php?plugin=fpp-zettle&file=zettle.css&nopage=1">
    <script src="https://unpkg.com/gridjs/dist/gridjs.umd.js"></script>
    <script type="text/javascript" src="/plugin.php?plugin=fpp-zettle&file=zettle.js&nopage=1"></script>
</head>
<body>
<?php if ($pluginJson['client_id'] != '' && count($pluginJson['subscriptions']) > 0) { ?>
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
<h3>Transactions (Total: <?php echo getTransactionsTotal(); ?>)</h3>
<div id="transactions"></div>
<br>
<input id="clear_transactions" class="buttons" value="Clear Transactions">
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
        server: {
            url: '/api/configfile/plugin.fpp-zettle-transactions.json',
            then: data => data.map(card => [card.timestamp, card.amount, card.userDisplayName])
        }
    });
    grid.render(document.getElementById("transactions"));

    setInterval(function() {
        grid.updateConfig({
            server: {
                url: '/api/configfile/plugin.fpp-zettle-transactions.json',
                then: data => data.map(card => [card.timestamp, card.amount, card.userDisplayName])
            }
        }).forceRender();
    }, 30000);

</script>
</body>
<?php } else { ?>
    <?php if ($pluginJson['client_id'] == '') { ?>
        <p>You need to configure this plugin before you can see the status. Click here to get to <a href="<?php echo $setupUrl; ?>">setup</a></p>
    <?php } ?>
    <?php if (count($pluginJson['subscriptions']) == 0) { ?>
        <p>You have <strong>Client ID</strong> and <strong>Client Secret</strong> setup, now you need to create a Subscription to link FPP with Zettle. Click here to <a href="<?php echo $subscriptionUrl; ?>">create subscription</a></p>
    <?php } ?>
<?php } ?>
