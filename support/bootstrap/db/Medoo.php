<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace support\bootstrap\db;

use support\medoo\Db;
use Webman\Bootstrap;
use Workerman\Timer;
use Workerman\Worker;

/**
 * Class Session
 * @package support
 */
class Medoo implements Bootstrap
{
    /**
     * @param Worker $worker
     * @return void
     */
    public static function start($worker)
    {
        if (!class_exists("\Medoo\Medoo")) {
            return;
        }
        Timer::add(10, function () {
            list($dbs, $dbs_time) = Db::allInstances();
            /**
             * @var \Medoo\Medoo $db
             */
            foreach ($dbs as $connection_name => $db) {
                if (isset($dbs_time[$connection_name]) && time() > $dbs_time[$connection_name] + 45) {
                    $db->query('select 1 limit 1');
                }
            }
        });
    }
}