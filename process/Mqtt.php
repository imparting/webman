<?php

namespace process;

use Workerman\Mqtt\Client;

class Mqtt
{
    public function onWorkerStart()
    {
        $server = '172.21.11.46';
        $port = 1883;
        $client_id = 'ndz_monitor_20210518';

        $mqtt = new Client("mqtt://{$server}:{$port}", ['client_id' => $client_id, 'connect_timeout' => 3]);
        $mqtt->onConnect = function ($mqtt) {
            $mqtt->subscribe('DataReceive/#');
        };
        $mqtt->onMessage = function ($topic, $content) {
            echo "================" . date('Y-m-d H:i:s') . " =============" . PHP_EOL;
            echo $topic . PHP_EOL;
            echo print_r(json_decode($content, true)) . PHP_EOL;
        };
        $mqtt->connect();
    }

}