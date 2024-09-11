CREATE DATABASE apws;

GRANT ALL PRIVILEGES ON apws.* TO 'apwsUser'@'127.0.0.1' IDENTIFIED BY 'apwsPassword';
FLUSH PRIVILEGES;

CREATE TABLE `commandQueue` (
    `queueId` INT unsigned NOT NULL AUTO_INCREMENT,
    `commandId` TINYINT unsigned NOT NULL DEFAULT 0,
    `deviceId` TINYINT unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY (`queueId`),
        KEY `idxDeviceId` (`deviceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `actionLog` (
    `logId` INT unsigned NOT NULL AUTO_INCREMENT,
    `logTime` INT unsigned NOT NULL DEFAULT 0,
    `logType` VARCHAR(255) NOT NULL DEFAULT '',
    `ipAddress` VARCHAR(255) NOT NULL DEFAULT '',
    `deviceId` TINYINT unsigned NOT NULL DEFAULT 0,
    `logMessage` VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`logId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sensorData` (
    `dataId` INT unsigned NOT NULL AUTO_INCREMENT,
    `logTime` INT unsigned NOT NULL DEFAULT 0,
    `deviceId` TINYINT unsigned NOT NULL DEFAULT 0,
    `dataValue` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`dataId`),
        KEY `idxDeviceId` (`deviceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
