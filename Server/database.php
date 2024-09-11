<?php

class Db
{
    /** @var PDO */
    private $_db;

    /** @var int */
    private $_lastAffectedRowCount = 0;

    /** @var Db */
    private static $_instance = NULL;

    /**
     * Instance of the database
     * @return Db
     */
    public static function getInstance()
    {
        if (NULL === self::$_instance) {
            self::$_instance = new self(
                Config::MYSQL_HOST,
                Config::MYSQL_PORT,
                Config::MYSQL_USERNAME,
                Config::MYSQL_PASSWORD,
                Config::MYSQL_DATABASE);
        }
        return self::$_instance;
    }

    /**
     * Connect to an ODBC database using driver invocation.
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     * @param string $database
     */
    private function __construct($host, $port, $username, $password, $database)
    {
        $dsn = 'mysql:dbname='.$database.';host='.$host.';port='.(int)$port.';charset=utf8';
        $this->_db = new PDO($dsn, $username, $password);
        $this->_db->exec("SET NAMES 'utf8';");
        $this->_db->exec("SET SESSION sql_mode=''STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
    }

    /**
     * @return void
     */
    private function __clone() {}

    /**
     * Shutdown the database connection and release resources.
     * @return void
     */
    public function __destruct()
    {
        $this->_db = NULL;
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     * @param string $sql
     * @return bool|int
     */
    public function execute($sql)
    {
        return $this->_db->exec($sql);
    }

    /**
     * Executes an SQL statement, returning the result as an array
     * @param string $sql
     * @param bool $firstEntryOnly
     * @param bool $fetchColumns
     * @return array
     */
    public function query($sql, $firstEntryOnly=false, $fetchColumns=false)
    {
        /* @var PDOStatement $sqlStatement */
        $sqlStatement = $this->_db->query($sql);

        if ($sqlStatement === false) {
            return [];
        }

        if ($fetchColumns === true) {
            return $sqlStatement->fetchAll(PDO::FETCH_COLUMN);
        }

        $this->_lastAffectedRowCount = $sqlStatement->rowCount();
        if ($firstEntryOnly === true) {
            return $sqlStatement->fetch(PDO::FETCH_ASSOC);
        } else {
            return $sqlStatement->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}