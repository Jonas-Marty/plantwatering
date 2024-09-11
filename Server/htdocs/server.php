<?php

/**
 * Automatic Plant Watering System (APWS) - Server control v2.0
 * (c) 2021 Christian Grieger (elektro.turanis.de)
 *
 * Hint: Customize to your needs in config.php
 */

define('BASEPATH', dirname(dirname(__FILE__)));

require_once BASEPATH.'/config.php';
require_once BASEPATH.'/database.php';
require_once BASEPATH.'/graph.php';
require_once BASEPATH.'/apws.php';

Apws::authorize();

if (isset($_POST['nextCommand']) && isset($_POST['deviceId']) ) {
    Apws::pushCommand((int)$_POST['nextCommand'], (int)$_POST['deviceId']);
}

// IoT device request the next command
if (isset($_GET['request'])) {
    exit((string)Apws::popCommand((int)$_GET['request']));
}

// IoT device requests logging
if (isset($_GET['log'])) {
    Apws::authorize();
    $logData = explode(',', $_GET['log']);
    if (!is_array($logData) || count($logData) != 3) {
        Apws::logAction('SERVER-ERROR', 0, 'Cannot log invalid data from IoT device', true);
    }
    Apws::logAction($logData[0], $logData[1], $logData[2], true);
}

?>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title>APWS Server control</title>
        <link rel="stylesheet" type="text/css" href="apws.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    </head>
    <body>
        <header>
            <a title="refresh page now!" href="<?php print $_SERVER['PHP_SELF'].'?psk='.$_GET['psk']; ?>">↕</a>
            <h1>APWS - Server control</h1>
            <p>Automatic plant watering system | &copy; 2021 Christian Grieger</p>
        </header>

        <div class="content">
            <h2>Plant moisture data</h2>
            <div class="graphs">
                <?php
                    $graphData = '';
                    foreach (Config::$devices as $deviceId => $device) {
                        $graphData .= Graph::getGraph($deviceId);
                    }

                    if ($graphData == '') {
                        print '<p>- empty -</p>';
                    } else {
                        print $graphData;
                    }
                ?>
                <div style="clear:both;"></div>
            </div>

            <h2>Manual commands</h2>
            <form method="post" action="">
                <table>
                    <tr>
                        <th><label for="nextCommand">Next command:</label></th>
                        <td>
                            <select id="nextCommand" name="nextCommand">
                                <option value="0">-- please choose --</option>
                                <?php
                                foreach (Config::$commands as $commandId => $command) {
                                    print '<option value="'.$commandId.'">'.$command.'</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="deviceId">Plant:</label></th>
                        <td>
                            <select id="deviceId" name="deviceId">
                                <option value="0">-- please choose --</option>
                                <?php
                                foreach (Config::$devices as $deviceId => $device) {
                                    print '<option value="'.$deviceId.'">'.$device.'</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <input type="submit" value="add to queue"/>
            </form>

            <h2>Command queue</h2>
            <?php
                $queue = Apws::getQueue();

                if (count($queue) > 0) {
                    print '<table>';
                    foreach ($queue as $entry) {
                        print '<tr>';
                        print '<td>' . Config::$devices[$entry['deviceId']].':</td>';
                        print '<td>' . Config::$commands[$entry['commandId']] . '</td>';
                        print '</tr>';
                    }
                    print '</table>';
                } else {
                    print '<p>- empty -</p>';
                }
            ?>

            <h2>History</h2>
            <?php
                $logs = Apws::getLog();

                if (count($logs) > 0) {
                    print '<table class="history">';
                    foreach (Apws::getLog() as $entry) {
                        $device = '-';
                        $deviceStyle = '';
                        if (isset(Config::$devices[$entry['deviceId']])) {
                            $device = Config::$devices[$entry['deviceId']];
                            $deviceStyle = ' style="background-color:'.Config::$colors[$entry['deviceId']].';color:#FFFFFF;"';
                        }

                        print '<tr>';
                        print '<td class="date">' . date('d.m.Y H:i:s', $entry['logTime']) . '</td>';
                        print '<td class="ip">' . $entry['ipAddress'] . '</td>';
                        print '<td'.$deviceStyle.'>' . $device . '</td>';
                        print '<td>' . $entry['logMessage'] . '</td>';
                        print '</tr>';
                    }
                    print '</table>';
                } else {
                    print '<p>- empty -</p>';
                }
            ?>

        </div>
    </body>
</html>