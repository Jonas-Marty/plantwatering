<?php

class Graph
{
    const MAX_HEIGHT = 150;
    const DATA_AMOUNT = 50;

    public static function getGraph($deviceId)
    {
        $db = Db::getInstance();
        $dbRes = $db->query('SELECT * FROM `sensorData` WHERE `deviceId`='.$deviceId.' ORDER BY `dataId` DESC LIMIT '.self::DATA_AMOUNT.';');

        if (count($dbRes) == 0) {
            return '';
        }

        $maxValue = 0;
        foreach ($dbRes as $dbRow) {
            $maxValue = max($maxValue, (int)$dbRow['dataValue']);
        }

        $graphWidth = count($dbRes) * 7;
        $result = '<div class="graph" style="width:'.$graphWidth.'px;">';
        foreach ($dbRes as $dbRow) {
            $val = (int)$dbRow['dataValue'];
            $height = (int)round($val/$maxValue * self::MAX_HEIGHT);
            $title = date('d.m.Y H:i:s', $dbRow['logTime']) . ' - '.$val;
            $style = 'height:'.$height.'px;background-color:'.Config::$colors[$deviceId].';';
            $result .= '<div class="bar" title="'.$title.'" style="'.$style.'"></div>';
        }
        $result .= '<div class="title">'.Config::$devices[$deviceId].'</div>';
        $result .= '</div>';

        return $result;
    }
}