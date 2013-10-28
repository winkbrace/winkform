<?php

/**
 * WinkForm configuration file
 *
 * In this file users can set the default settings for their WinkForms
 */

return array(

    // error message language
    'locale' => 'nl',
    // path to language files
    'lang_location' => WINKFORM_PATH . 'lang',

    // date format
    'date_format' => 'd-m-Y',
    // url to calendar image
    'calendar_image' => (defined('BASE_URL') ? BASE_URL : '/') . 'images/helveticons/32x32/Calendar alt 32x32.png',

);
