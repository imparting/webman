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