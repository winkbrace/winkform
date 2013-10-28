<?php namespace WinkForm;

/**
 * file containing the required setup
 * @author b-deruiter
 */


// constants
if (! defined('BRCLR'))
    define('BRCLR', '<br class="clear" />');

if (! defined('WINKFORM_PATH'))
    define('WINKFORM_PATH', __DIR__.'/');


// helper functions
require_once 'helpers.php';

// composer autoloader
require_once realpath(WINKFORM_PATH.'../../vendor/autoload.php');
