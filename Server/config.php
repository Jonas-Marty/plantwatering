<?php

date_default_timezone_set('UTC');

class Config
{
    const PSK = 'mySecretPsk';

    const MYSQL_HOST     = 'mysql';
    const MYSQL_PORT     = 3306;
    const MYSQL_USERNAME = 'ff';
    const MYSQL_PASSWORD = 'askldjeimghhgwese';
    const MYSQL_DATABASE = 'apws';

    // valid commands
    public static $commands = [
        1 => 'Pause automatic watering',
        2 => 'Resume automatic watering',
        3 => 'Manual watering',
        4 => 'Manual soil moisture measurement',
        5 => 'Increase moisture threshold',
        6 => 'Increase moisture threshold'
    ];

    // valid devices (=plants)
    public static $devices = [
        1 => 'Plant I.',
        2 => 'Plant II.',
        3 => 'Plant III.',
        4 => 'Plant IV.'
    ];

    // different colors for the devices (=plants)
    public static $colors = [
        1 => '#63a911',
        2 => '#11a972',
        3 => '#117ca9',
        4 => '#6011a9'
    ];
}