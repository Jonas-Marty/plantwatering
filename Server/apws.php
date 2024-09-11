<?php

class Apws
{
    public static function authorize()
    {
        if (!isset($_GET['psk'])) {
            exit(0);
        }
        if ($_GET['psk'] != Config::PSK) {
            self::logAction('SERVER-ERROR', 0, 'Unauthorized request', true);
        }
    }

    /**
     * @param string $type
     * @param int    $deviceId
     * @param string $message
     * @param bool   $doExit
     */
    public static function logAction ($type, $deviceId, $message, $doExit = false)
    {
        $db = Db::getInstance();

        if ($type == 'MEASUREMENT') {
            $db->execute('INSERT INTO `sensorData` (`logTime`, `deviceId`, `dataValue`) VALUES('.time().', '.(int)$deviceId.', '.(int)$message.');');
            exit(0);
        }

        $ipAddr = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'?';
        $db->execute('INSERT INTO `actionLog` (`logTime`, `logType`, `ipAddress`, `deviceId`, `logMessage`) VALUES('.time().', "'.$type.'", "'.$ipAddr.'", '.$deviceId.', "'.$message.'");');

        if ($doExit) {
            exit(0);
        }
    }

    /**
     * Adds a new command to the queue (push)
     * @param int $commandId
     * @param int $deviceId
     */
    public static function pushCommand($commandId, $deviceId)
    {
        if (!isset(Config::$commands[$commandId]) || !isset(Config::$devices[$deviceId])) {
            return;
        }

        $db = Db::getInstance();
        $db->execute('INSERT INTO `commandQueue` (`commandId`, `deviceId`) VALUES('.$commandId.', '.$deviceId.');');

        self::logAction('SERVER-INFO', $deviceId, 'Added command #'.$commandId.' («'.Config::$commands[$commandId].'») to queue');
    }

    /**
     * Fetches the next command and removes it from the queue (pop)
     * @param int $deviceId
     * @return int
     */
    public static function popCommand($deviceId)
    {
        $db = Db::getInstance();
        $command = $db->query('SELECT * FROM `commandQueue` WHERE `deviceId`='.$deviceId.' ORDER BY `queueId` ASC LIMIT 1;', true);

        if (!isset($command['queueId'])) {
            return 0;
        }

        $result = $db->execute('DELETE FROM `commandQueue` WHERE `queueId`='.(int)$command['queueId'].' LIMIT 1;');
        if ($result == 0) {
            self::logAction('SERVER-ERROR', $deviceId, 'Cannot pop next command from queue', true);
        }

        $commandId = (int)$command['commandId'];
        self::logAction('SERVER-INFO', $deviceId, 'Delivered command #'.$commandId.' («'.Config::$commands[$commandId].'»)');
        return $commandId;
    }

    /**
     * @return array
     */
    public static function getQueue()
    {
        $db = Db::getInstance();
        return $db->query('SELECT * FROM `commandQueue` ORDER BY `queueId` ASC;');
    }

    /**
     * @return array
     */
    public static function getLog()
    {
        $db = Db::getInstance();
        return $db->query('SELECT * FROM `actionLog` ORDER BY `logId` DESC LIMIT 30;');
    }
}