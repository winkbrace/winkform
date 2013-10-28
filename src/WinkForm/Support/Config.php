<?php namespace WinkForm\Support;
/**
 * Config.php
 * reads the config.php file and returns the array
 * Part of WinkForm
 * Created by Bas de Ruiter
 * Date: 28-10-2013
 */
class Config
{
    /**
     * @var array
     */
    protected static $config;

    /**
     * get the contents of the config.php file as array
     * @return array
     */
    protected static function getConfig()
    {
        if (empty(static::$config))
            static::$config = require WINKFORM_PATH.'config.php';

        return static::$config;
    }

    /**
     * get config setting belonging to key
     * @param $key
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function get($key)
    {
        $config = static::getConfig();
        if (! array_key_exists($key, $config))
            throw new \InvalidArgumentException('given key does not exist in config.php');

        return $config[$key];
    }

    /**
     * get config array
     * @return array
     */
    public static function all()
    {
        return static::getConfig();
    }
}
