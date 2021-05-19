<?php


namespace support\medoo;


class Db
{
    /**
     * 连接单例存储数组
     * @var array $_instances
     */
    private static $_instances = [];

    private static $_instances_last_active_time = [];

    /**
     * 数据库配置
     * @var null
     */
    private static $_config = null;

    public static function allInstances(): array
    {
        return [static::$_instances, self::$_instances_last_active_time];
    }

    /**
     * 获取数据库连接单例
     * @param $connection_name
     * @return Medoo
     * @throws MedooErrorException
     */
    public static function getInstance($connection_name): Medoo
    {
        if (!array_key_exists($connection_name, static::$_instances)) {
            if (!static::$_config) static::$_config = config('medoo');
            if (isset(static::$_config[$connection_name])) {
                $instance = new Medoo(static::$_config[$connection_name]);
                self::$_instances[$connection_name] = $instance;
            } else {
                throw new MedooErrorException("'{$connection_name}' not exists on config/medoo.php");
            }
        }
        self::$_instances_last_active_time[$connection_name] = time();
        return self::$_instances[$connection_name];
    }
}
