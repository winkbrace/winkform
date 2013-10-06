<?php

/**
 * TODO these functions are not used in the library. Move to functions.php:
 * isDate
 * dateYmd
 * isValidEmail
 * array_join
 * any_in_array
 * array_is_numeric
 */

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

if (! function_exists('isDate'))
{
    /**
     * check of opgegeven datum een geldige dd-mm-yyyy datum is
     *
     * @param string $date
     * @return boolean
     */
    function isDate($date)
    {
        if (! strstr($date, '-'))
            return false;

        $arr = explode('-', $date);

        if (count($arr) != 3)  // dd, mm en yyyy
            return false;

        list($d, $m, $y) = $arr;
        if (! checkdate($m, $d, $y))  // php built in function to check if date is valid
            return false;

        return true;
    }
}

if (! function_exists('dateYmd'))
{
    /**
     * convert given date string to Ymd date. Handy for determining later or earlier dates
     * @param string $date
     * @return string Y-m-d
     */
    function dateYmd($date)
    {
        if (isDate($date)) // then it's dd-mm-yyyy
        {
            $arr = explode('-', $date);
            return $arr[2].$arr[1].$arr[0];
        }
        else // dd-mmm-yy, mm-dd-yyyy or yyyy-mm-dd
        {
            return date('Ymd', strtotime($date));
        }
    }
}

if (! function_exists('isValidEmail'))
{
    /**
     * Does email address look like a valid email?
     *
     * @param string $email
     * @return boolean
     */
    function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (! function_exists('array_join'))
{
    /**
     * array left join array2 on (array of) $fields = (array of) $fields
     * notice: rows of array2 can be joined multiple times to array, but rows of array are considered unique.
     *
     * @param array $array
     * @param array $array2
     * @param mixed $fields array of fields (or single value) to join the arrays on
     * @return array
     */
    function array_join($array, $array2, $fields)
    {
        if (! is_array($array) || ! is_array($array2))
            return false;

        // create array if single value is given
        if (! is_array($fields))
            $fields = array($fields);

        $count = count($fields);  // store count in variable

        foreach ($array as $nr => $row)
        {
            foreach($array2 as $row2)
            {
                // check all join fields
                $c = $count;
                foreach ($fields as $field)
                {
                    if ($row[$field] == $row2[$field])
                       $c--;
                }

                // if all fields are the same counter $c will be 0
                if ($c == 0)
                {
                    $array[$nr] += $row2;  // add data of found row to array
                    break;  // stop after first found match and go to next row
                }
            }
        }

        return $array;
    }
}

if (! function_exists('any_in_array'))
{
    /**
     * Check if any value in array one exists in array two
     *
     * @param array $array1
     * @param array $array2
     * @return boolean
     */
    function any_in_array($array1, $array2)
    {
        foreach ($array1 as $a)
        {
            if (in_array($a, $array2))
                return true;
        }

        return false;
    }
}

if (! function_exists('array_is_numeric'))
{
    /**
     * check if given array is numeric
     *
     * @param array $array
     * @return boolean
     */
    function array_is_numeric($array)
    {
        // this is cheaper than return $array === array_values($array)
        // because it will spot an assoc array immediately and only loop over the array if it is numeric,
        // instead of first creating a copy array and then checking the 2 complete arrays
        foreach ($array as $key => $val)
        {
            if (! is_int($key))
                return false;
        }

        return true;
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

if (! function_exists('copySharedAttributes'))
{
    /**
     * Copy attributes that are in both $from and $to from $from into $to
     * Handy for sibling objects (like the Input children)
     * This acts like an object cast in Java: Car car = (Car) volvo;
     * @param object $to
     * @param object $from
     * @param array $excludes array of properties to exclude from copying
     */
    function copySharedAttributes(&$to, $from, $excludes = array())
    {
        if (! is_object($to) && ! is_object($from))
            return false;

        // get the attributes of both objects and loop through all attributes that exist in both objects
        $rf = new \ReflectionObject($from);
        $rt = new \ReflectionObject($to);
        foreach ($rf->getProperties() as $propFrom)
        {
            $propFrom->setAccessible(true);
            $name = $propFrom->getName();
            $value = $propFrom->getValue($from);

            if ($rt->hasProperty($name) && ! in_array($name, $excludes))
            {
                $propTo = $rt->getProperty($name);
                $propTo->setAccessible(true);
                $propTo->setValue($to, $value);
            }
        }
    }
}
