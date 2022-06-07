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
?>
<style type="text/css">
    .tg {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .tg td {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg th {
        border-color: black;
        border-style: solid;
        border-width: 1px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg .tg-3xvn {
        font-family: inherit;
        font-weight: bold;
        text-align: center;
        vertical-align: top
    }

    .tg .tg-0lax {
        text-align: left;
        vertical-align: top
    }

    .hidetext {
        -webkit-text-security: disc; /* Default */
    }
</style>
<script type="text/javascript" src="/plugin.php?plugin=fpp-zettle&file=zettle.js&nopage=1"></script>
<?php if ($pluginJson['client_id'] != '') { ?>
    <table class="tg">
        <thead>
        <tr>
            <th class="tg-3xvn" colspan="2"></th>
            <th class="tg-3xvn" colspan="3">Subscriptions</th>
            <th class="tg-3xvn" colspan="1"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="tg-3xvn">Client ID</td>
            <td class="tg-3xvn">Client Secret</td>
            <td class="tg-3xvn">Email</td>
            <td class="tg-3xvn">Destination</td>
            <td class="tg-3xvn">Status</td>
            <td class="tg-3xvn">Trigger Effect</td>
        <tr>

        <tr>
            <td class="tg-0lax"><?php echo $pluginJson['client_id']; ?></td>
            <td class="hidetext"><?php echo substr($pluginJson['client_secret'], 0, 8); ?></td>
            <td class="tg-0lax"><?php echo $pluginJson['subscriptions']['contactEmail']; ?></td>
            <td class="tg-0lax"><?php echo $pluginJson['subscriptions']['destination']; ?></td>
            <td class="tg-0lax"><?php echo $pluginJson['subscriptions']['status']; ?></td>
            <td class="tg-0lax"><?php echo $pluginJson['effect']; ?></td>
        </tr>
        </tbody>
    </table>

    <br><h3>Transactions</h3>
    <table class="tg">
        <thead>
        <tr>
            <th class="tg-3xvn">Timestamp</th>
            <th class="tg-3xvn">Amount</th>
            <th class="tg-3xvn">User Display Name</th>
        </tr>
        </thead>
        <tbody>
        <?
        if (count($plugindatabase) > 0) {
            $total = 0;
            foreach (array_reverse($plugindatabase) as $d) {
                // $payload = json_decode($d['payload'], true);
                echo '<tr>';
                echo '<td class="tg-0lax">' . date('d/m/y H:i', ceil($d['timestamp'] / 1000)) . '</td>';
                echo '<td class="tg-0lax">' . $d['formatted_amount'] . '</td>';
                echo '<td class="tg-0lax">' . $d['userDisplayName'] . '</td>';
                echo '</tr>';
                $total += $d['amount'];
            }
            echo '<tr><td>Total:</td><td colspan="2">' . number_format($total, 2) . '</td></tr>';
        } else {
            echo '<tr><td colspan="3">No Transactions Yet</td></tr>';
        }
        ?>
        </tbody>
    </table>
    <br>
    <input id="clear_transactions" class="buttons" value="Clear Transactions">
<?php } else { ?>
<p>You need to configure this plugin before you can see the status. Click here to get to <a href="<?php echo $setupUrl; ?>">setup</a></p>
<?php } ?>
