<?php

if (! function_exists('str_like'))
{
    /**
     * does the string $haystack confirm to the sql-style $like condition
     * N.B. For now it only supports % and not underscores!
     * @param string $haystack
     * @param string $like
     * @return boolean
     */
    function str_like($haystack, $like)
    {
        $subs = explode('%', $like);
        $lastPos = 0;
        foreach ($subs as $sub)
        {
            if (empty($sub))  // starts or ends with %
                continue;

            $pos = strpos($haystack, $sub, $lastPos);
            if ($pos === false)
                return false;

            $lastPos = $pos;
        }

        return true;
    }
}

if (! function_exists('isPosted'))
{
    /**
     * Check if $name is posted and not empty
     * If $value is given, then check if the posted value equals the given value
     *
     * @param string $name
     * @param string $value
     * @return boolean
     */
    function isPosted($name, $value = null)
    {
        if (! isset($_POST[$name]))
            return false;

        if (is_array($_POST[$name]))
        {
            if (! empty($value))
                return in_array($value, $_POST[$name]);
            elseif (count($_POST[$name]) == 1 && isset($_POST[$name][0]) && empty($_POST[$name][0]))
                return false;
            else
                return count($_POST[$name]) > 0;
        }
        else
        {
            if (! empty($value))
                return $_POST[$name] == $value;
            else
                return ! empty($_POST[$name]);
        }
    }
}

if (! function_exists('xsschars'))
{
    /**
    * Convert special characters to HTML entities. All untrusted content
    * should be passed through this method to prevent XSS injections.
    *
    * echo xsschars($username);
    *
    * @param string   $value         string to convert
    * @param boolean  $doubleEncode  encode existing entities
    * @return string
    */
    function xsschars($value, $doubleEncode = true)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }
}

if (! function_exists('error'))
{
    /**
     * encapsulate message with error div
     * @param string $msg
     * @return string html div error message
     */
    function error($msg)
    {
        $div = '<div class="error">';

        // first remove error divs from $msg
        if (strstr($msg, $div))
        {
            // loop through all error divs
            while (($pos = strrpos($msg, $div)) !== false) // get last occurrence of error div
            {
                // cut out error div
                $start = substr($msg, 0, $pos);
                $end = substr($msg, $pos + strlen($div));
                // find matching </div>
                $endpos = strpos($end, "</div>\n");
                if ($endpos === false)
                    $endpos = strpos($end, "</div>");
                // cut out </div>
                $msg = $start . substr($end, 0, $endpos) . substr($end, $endpos + 6);
            }
        }

        // then surround $msg with (new) error div
        return $div.$msg."</div>\n";
    }
}

if (! function_exists('success'))
{
    /**
     * encapsulate message with success div
     * @param string $msg
     * @return string html div error message
     */
    function success($msg)
    {
        return '<div class="success">'.$msg."</div>\n";
    }
}

if (! function_exists('info'))
{
    /**
     * encapsulate message with blue 'info' div
     * @param string $msg
     * @return string html div info message
     */
    function info($msg)
    {
        return '<div class="info">'.$msg."</div>\n";
    }
}

if (! function_exists('coalesce'))
{
    /**
     * This function will take an undefined amount of arguments and use the first value that is not empty
     */
    function coalesce()
    {
        $args = func_get_args();
        foreach ($args as $arg)
        {
            if (! empty($arg))
                return $arg;
        }

        // if everything was empty, then return null
        return null;
    }
}

if (! function_exists('initcap'))
{
    /**
     * copy Oracle's initcap function. php doesn't lowercase the rest by default with ucfirst.
     * @param string $string
     * @return string
     */
    function initcap($string)
    {
        return ucfirst(strtolower($string));
    }
}

if (! function_exists('getFiles'))
{
    /**
     * get array of all files in the given directory and it's subdirectories
     * You can filter the output by extension
     *
     * @param string $dir
     * @param string $extension
     * @return array $files
     */
    function getFiles($dir, $extension = null)
    {
        $files = scandir($dir);
        foreach ($files as $i => $entry)
        {
            if (substr($entry, 0, 1) != '.')
            {
                if (is_dir($dir . '/' . $entry))
                {
                    // get subdir recursively and add to end of $files array
                    foreach (getFiles($dir . '/' . $entry, $extension) as $subEntries)
                        $files[] = $subEntries;
                }
                elseif (empty($extension) || substr($entry, -strlen($extension)) == $extension)
                {
                    $files[] = $dir . '/' . $entry;
                }
            }

            unset($files[$i]); // remove initial value, because we always add to array
        }

        return array_values($files);
    }
}

if (! function_exists('is_blank'))
{
    /**
     * A version of empty() that accepts these as valid not empty values:
     * <ul>
     *    <li>0 (0 as an integer)</li>
     *    <li>0.0 (0 as a float)</li>
     *    <li>"0" (0 as a string)</li>
     * </ul>
     *
     * @param mixed $value
     * @return boolean
     */
    function is_blank($value)
    {
        return empty($value) && ! is_numeric($value);
    }
}

if (! function_exists('get_winkform_config'))
{
    /**
     * get the contents of the config file as an array
     * @return array
     */
    function get_winkform_config()
    {
        return require CONFIG_PATH . 'config.php';
    }
}
