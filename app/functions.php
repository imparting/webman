<?php
/**
 * Here is your custom functions.
 */

use support\medoo\Db;

if (!function_exists('DBC')) {
    /**
     * @param $connection_name
     * @return \support\medoo\Medoo
     * @throws \support\medoo\MedooErrorException
     */
    function DBC($connection_name)
    {
        return Db::getInstance($connection_name);
    }
}

if (!function_exists('beginTransaction')) {
    function beginTransaction($connection_name)
    {
        Db::getInstance($connection_name)->getPdo()->beginTransaction();
    }
}

if (!function_exists('commit')) {
    function commit($connection_name)
    {
        Db::getInstance($connection_name)->getPdo()->commit();
    }
}

if (!function_exists('rollBack')) {
    function rollBack($connection_name)
    {
        Db::getInstance($connection_name)->getPdo()->rollBack();
    }
}