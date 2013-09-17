<?php
namespace WinkForm;

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

/**
 * Check of $name gepost is en of het een waarde heeft.
 * Als $value meegeven wordt, check dan of de gepostte waarde daaraan gelijk is.
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

/**
 * array left join array2 on (array of) $fields = (array of) $fields
 * notice: rows of array2 can be joined multiple times to array, but rows of array are considered unique.
 *
 * @param array $array
 * @param array $array2
 * @param mixed $fields array of fields (or single value) to join the arrays on
 * @return joined array
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
        foreach($array2 as $nr2 => $row2)
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

/**
 * secho - secure echo function
 * @param $value
 */
function secho($value)
{
    echo xsschars($value);
}

/**
 * get the first and last day of given period
 * All periods that are by default used in the ReportForm marketingwebpages, including day and year,
 * are accepted for ease of programming
 *
 * @param $period
 * @param period type $type ('day','week','month','quarter' or 'year')
 * @return array($firstDate, $lastDate)
 */
function getPeriodFirstAndLast($period, $type)
{
    try
    {
        $type = strtolower($type);
        if ($type == 'day')
        {
            $parts = explode('-', $period);
            $day = $parts[2].'-'.$parts[1].'-'.$parts[0];
            return array($day, $day);
        }
        elseif ($type == 'week')
        {
            if (strlen($period) != '7' || strstr($period, '-') === false)
                throw new \Exception('Invalid week period given (IYYY-IW) - '.$period);
            
            $year = substr($period, 0, 4);
            $week = substr($period, -2);
            // jan 4th is always in ISO week 1
            $date = strtotime($year.'0104 +'.($week - 1).' weeks');
            // first day of week is sunday. If date('w') == 0 the it's sunday
            $first = date('w', $date) == 0 ? $date : strtotime('last sunday', $date);
            $last = date('w', $date) == 6 ? $date : strtotime('next saturday', $date);
            
            return array(date('d-m-Y', $first), date('d-m-Y', $last));
        }
        elseif ($type == 'month')
        {
            if (strlen($period) != '7' || strstr($period, '-') === false)
                throw new \Exception('Invalid month period given (YYYY-MM) - '.$period);
            
            $year = substr($period, 0, 4);
            $month = substr($period, -2);
            
            $first = strtotime($year.$month.'01');
            $last = strtotime($year.$month.date('t', $first)); // date('t') returns number of days in given month
            
            return array(date('d-m-Y', $first), date('d-m-Y', $last));
        }
        elseif ($type == 'quarter')
        {
            if (strlen($period) != '6' || strstr($period, '-') === false)
                throw new \Exception('Invalid quarter period given (YYYY-Q) - '.$period);
                
            $year = substr($period, 0, 4);
            $quarter = substr($period, -1);
            $firstMonth = str_pad(1 + (($quarter - 1) * 3), 2, '0', STR_PAD_LEFT);
            $lastMonth = str_pad($quarter * 3, 2, '0', STR_PAD_LEFT);
            
            $first = strtotime($year.$firstMonth.'01');
            $last = strtotime($year.$lastMonth.date('t', strtotime($year.$lastMonth.'01')));
            
            return array(date('d-m-Y', $first), date('d-m-Y', $last));
        }
        elseif ($type == 'year')
        {
            if (strlen($period) != '4')
                throw new \Exception('Invalid year period given (YYYY) - '.$period);
                
            $first = strtotime($period.'0101');
            $last = strtotime($period.'1231');
            
            return array(date('d-m-Y', $first), date('d-m-Y', $last));
        }
        
        // else invalid input
        throw new \Exception('Invalid period type given - '.$type);
    }
    catch (FormException $e)
    {
        echo $e->getMessage();
        return false;
    }
}

/**
 * format number to euro currency string
 * @param numeric $number
 * @return euro currency formatted string
 */
function euro($number)
{
    return '&euro;.'.number_format($number, 2, ',', '.');
}

/**
 * encapsulate message with error div
 * @param string $msg
 * @return html div error message
 */
function error($msg)
{
    $div = '<div class="error">';

    // first remove error divs from $msg
    if (strstr($msg, $div))
    {
        // loop through all error divs
        while (($pos = strrpos($msg, $div)) !== false) // get last occurence of error div
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

/**
 * encapsulate message with success div
 * @param string $msg
 * @return html div error message
 */
function success($msg)
{
    return '<div class="succes">'.$msg."</div>\n";
}

/**
 * encapsulate message with blue 'info' div
 * @param string $msg
 * @return html div info message
 */
function info($msg)
{
    return '<div class="info">'.$msg."</div>\n";
}


/**
 * Returns the position of the $nth occurrence of $needle in $haystack,
 * or false if it doesn't exist, or false when illegal parameters have been supplied.
 *
 * @param  string  $haystack   the string to search in.
 * @param  string  $needle     the string to search for.
 * @param  integer $nth        the number of the occurrence to look for.
 * @param  integer $offset     the position in $haystack to start looking for $needle.
 * @return MIXED   integer     either the position of the $nth occurrence of $needle in $haystack,
 * or boolean     false if it can't be found.
 */
function strnpos($haystack, $needle, $nth, $offset = 0)
{
    if (1 > $nth || 0 === strlen($needle))
        return false;
    
    //  $offset is incremented in the call to strpos, so make sure that the first call starts at the right position by initially decrementing $offset.
    --$offset;
    do
    {
        $offset = strpos($haystack, $needle, ++$offset);
    }
    while (--$nth && false !== $offset);
    
    return $offset;
}


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


/**
 * copy Oracle's initcap function. php doesn't lowercase the rest by default with ucfirst.
 * @param string $string
 */
function initcap($string)
{
    return ucfirst(strtolower($string));
}

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
                foreach (getFiles($dir . '/' . $entry, $extension) as $entry)
                    $files[] = $entry;
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

function testje($bla)
{
    if ($bla & 8)
        echo 'ok';
    
    
}

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
