<?php include $settings['pluginDirectory']."/fpp-zettle/pluginUpdate.php" ?>
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
    <style>
        .avatar{width:40px;min-width:40px;height:40px}
        .avatar.no-thumbnail{background-color:var(--color-300);font-weight:600;display:flex;align-items:center;justify-content:center}
        .rounded{border-radius:.25rem !important}
        .ms-3{margin-left:1rem !important}
    </style>
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

<?php
// echo date("Y-m-d", strtotime('monday this week')), "\n";
// echo date("Y-m-d", strtotime('sunday this week')), "\n";
?>

<div class="row g-3 mb-3 row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-2 row-cols-xl-3">
    <div class="col">
        <div class="alert-success alert mb-0">
            <div class="d-flex align-items-center">
                <div class="avatar rounded no-thumbnail bg-success text-light"><i class="fas fa-pound-sign fa-lg"></i></div>
                <div class="flex-fill ms-3 text-truncate">
                    <div class="h6 mb-0">Today</div>
                    <span class="small" id="today">£0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="alert-info alert mb-0">
            <div class="d-flex align-items-center">
                <div class="avatar rounded no-thumbnail bg-info text-light"><i class="fas fa-pound-sign fa-lg"></i></div>
                <div class="flex-fill ms-3 text-truncate">
                    <div class="h6 mb-0">Yesterday</div>
                    <span class="small" id="yesterday">£0</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="alert-success alert mb-0">
            <div class="d-flex align-items-center">
                <div class="avatar rounded no-thumbnail bg-success text-light"><i class="fas fa-pound-sign fa-lg"></i></div>
                <div class="flex-fill ms-3 text-truncate">
                    <div class="h6 mb-0">This Week</div>
                    <span class="small" id="this_week">£0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<h3>Transactions (Total: <?php echo getTransactionsTotal(); ?>)</h3>
<div id="transactions"></div>
<br>
<input id="clear_transactions" class="buttons" value="Clear Transactions">
<script>
    const grid = new gridjs.Grid({
        columns: [{
            id: 'timestamp',
            name: 'Time',
            width: '50%',
            formatter: (data) => {
                return new Date(data).toGMTString();
            }
        }, {
            id: 'amount',
            name: 'Amount £',
            width: '50%',
            formatter: (cell) => `£${cell}`
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
            then: data => data.map(card => [card.timestamp, card.amount])
        }
    });
    grid.render(document.getElementById("transactions"));

    setInterval(function() {
        grid.updateConfig({
            server: {
                url: '/api/configfile/plugin.fpp-zettle-transactions.json',
                then: data => data.map(card => [card.timestamp, card.amount])
            }
        }).forceRender();
    }, 30000);
    $(function () {
        function ajaxGet(url, feild) {
            $.ajax({
                type: "GET",
                url: url,
                success: function (data) {
                    $('span#' + feild).html(data);
                }
            });
        }

        ajaxGet('plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_purchases&nopage=1&option=today', 'today');
        ajaxGet('plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_purchases&nopage=1&option=yesterday', 'yesterday');
        ajaxGet('plugin.php?plugin=fpp-zettle&page=zettle.php&command=get_purchases&nopage=1&option=this_week', 'this_week');
    });
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
