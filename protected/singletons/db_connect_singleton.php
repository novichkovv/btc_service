<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 06.03.15
 * Time: 19:59
 */
class db_connect_singleton
{
    protected static $instance = array();
    private static $db;
    public $pdo;

    private function __clone() {}

    private function __construct($db, $db_host = null, $db_user = null, $db_password = null)
    {
        self::$db = $db;
        $dsn = 'mysql:host=' . ( $db_host ? $db_host : MDB_HOST ) . ';dbname=' . ( $db ? $db : MDB_NAME );
        $this->pdo = new custom_pdo_class($dsn, $db_user ? $db_user : MDB_USER, $db_password ? $db_password : MDB_PASSWORD);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS ,PDO::NULL_TO_STRING);
        $this->pdo->exec("SET sql_mode = ''");
        $this->pdo->exec("SET NAMES utf8");
    }

    public static function getInstance($db, $db_host = null, $db_user = null, $db_password = null)
    {
        if(!array_key_exists($db, self::$instance)) {
            self::$instance[$db] = new self($db, $db_host, $db_user, $db_password);
        }
        return self::$instance[$db];
    }
}