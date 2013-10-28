<?php namespace WinkForm\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

/**
 * Translator.php
 * simple wrapper class to create and maintain a single instance of Laravel's Translator
 * Part of WinkForm
 * Created by Bas de Ruiter
 * Date: 28-10-2013
 */
class Translator
{
    /**
     * @var \Illuminate\Translation\Translator
     */
    protected static $instance;

    /**
     * We create a translator object that searches for files in the 'lang' folder
     * lang/{locale}/{domain}.php
     * by default: lang/en/filename.php
     * @return \Illuminate\Translation\Translator
     */
    public static function getInstance()
    {
        if (empty(static::$instance))
        {
            $config = get_winkform_config();
            static::$instance = new \Illuminate\Translation\Translator(new FileLoader(new Filesystem, $config['lang_location']), $config['locale']);
        }

        return static::$instance;
    }

    /**
     * disabled constructor
     */
    protected function __construct() {}

}
