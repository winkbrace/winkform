<?php namespace WinkForm;

class Validate
{
    // attributes
    protected $valid;
    protected $message;
    
    
    /**
     * constructor
     *
     * @return Validate object
     */
    public function __construct()
    {
        $this->valid = true; // initialize to true
        $this->message = '';
    }
    
    
    /**
     * check if input value is numeric
     *
     * @param string $input
     * @return boolean
     */
    public function numeric($input)
    {
        if (is_numeric($input))
            return true;
        else
            return $this->invalidate($input, ' is not a valid number.');
    }
    
    /**
     * check if input value is a date dd-mm-yyyy
     *
     * @param string $value
     * @return boolean
     */
    public function date($value)
    {
        preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $value, $matches);
        if (isset($matches[1]) && isset($matches[2]) && isset($matches[3]) && checkdate($matches[2], $matches[1], $matches[3]))
            return true;
        else
            return $this->invalidate($value, ' is not a valid date.');
    }
    
    
    /**
     * Checks if the given date is greater than the minimum date
     *
     * @param string $value
     * @param int|string|\DateTime $minDate
     * @return boolean
     */
    public function minDate($value, $minDate = null)
    {
        // when null there is no minimum
        if (is_null($minDate))
            return true;
        
        $format = 'd-m-Y';
        $dateToCheck = new \DateTime($value);
        $dateMin = ($minDate instanceof \DateTime)
            ? $minDate    // A DateTime object containing the minimum date
            : new \DateTime('now');
        
        // number of days from today
        if (is_numeric($minDate))
        {
            $interval = \DateInterval::createFromDateString((int) $minDate . ' day');
            $dateMin->add($interval);
        }
        elseif($minDate instanceof \DateTime)
        {
            /* nothing to do here, but we want to keep it so it
               cascades through different types of minimum date */
        }
        // relative date
        else
        {
            if (strtotime($minDate) === false)
                return $this->invalidate($minDate, ' is not a valid relative date format.');

            $interval = \DateInterval::createFromDateString($minDate);
            $dateMin->add($interval);
        }
        
        // final check to see if the date is bigger than the minimum
        if ($dateToCheck > $dateMin)
            return true;
        
        $minDate = ($minDate instanceof \DateTime)
            ? $minDate->format($format)
            : $minDate;
        
        return $this->invalidate($value, ' is smaller than the minimum: ' . $minDate
                        . '. (' . $dateToCheck->format($format) . ' vs. ' . $dateMin->format($format) . ')' );
    }
    
    /**
     * check if input value is a valid email address
     *
     * @param string $email
     * @return boolean
     */
    public function email($email)
    {
        if (strstr($email, '<') && strstr($email, '>'))
        {
            $start = strpos($email, '<') + 1;
            $stop = strpos($email, '>');
            $email = substr($email, $start, ($stop - $start));
        }
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
            return $this->invalidate($email, ' is not a valid e-mail address.');
            
        return true;
    }
    
    /**
     * check if password is valid:
     * - at least 6 characters long
     * - consists of a mix of letters and other character
     *
     * @param string $password
     */
    public function password($password)
    {
        if (strlen($password) < 6)
            return $this->invalidate($password, 'Your password is not safe. It has to be at least 6 characters long.');
        
        $hasLetter = false;
        $hasNumber = false;
        for ($i = 0; $i < strlen($password); $i++)
        {
            if (is_numeric($password[$i]))
                $hasNumber = true;
            if (! is_numeric($password[$i]))
                $hasLetter = true;
                
            if ($hasLetter && $hasNumber)
                return true;
        }
        
        return $this->invalidate($password, 'Your password is not safe. Both letters and numbers are required.');
    }
    
    /**
     * check that input has at least the required minimum length
     *
     * @param string $input
     * @param int $length
     */
    public function minLength($input, $length)
    {
        if (strlen($input) >= $length)
            return true;
        else
            return $this->invalidate($input, " is smaller than the required minimum length of $length.");
    }
    
    /**
     * check that input has at most the allowed maximum length
     *
     * @param string $input
     * @param int $length
     */
    public function maxLength($input, $length)
    {
        if (strlen($input) <= $length)
            return true;
        else
            return $this->invalidate($input, " is bigger than the allowed maximum length of $length.");
    }
    
    /**
     * check that input has exactly the allowed length
     * @param string $input
     * @param int $length
     * @return boolean
     */
    public function hasLength($input, $length)
    {
        if (strlen($input) == $length)
            return true;
        else
            return $this->invalidate($input, " doesn't have the exact length of $length.");
    }
    
    /**
     * check that input contains the string search
     * @param string $input
     * @param string $search
     * @return boolean
     */
    public function contains($input, $search)
    {
        if (strpos($input, $search) !== false)
            return true;
        else
            return $this->invalidate($input, " doesn't contain the string $search.");
    }
    
    /**
     * check if given filename would be a valid filename
     *
     * @param string $filename
     */
    public function filename($filename)
    {
        $sanitizedName = preg_replace('/[^0-9a-z\.\_\-]/i', '', $filename);
        if ($sanitizedName == $filename && ! empty($filename))
            return true;
        else
            return $this->invalidate($filename, " is not a valid filename.");
    }
    
    /**
     * check if given filename would be a valid filename
     *
     * @param string $filename
     */
    public function isValidUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false)
            return true;
        else
            return $this->invalidate($url, " is not a valid url.");
    }
    
    /**
     * check if given postcode is a valid Nederlandse postcode
     * @param string $postcode
     */
    public function postcode($postcode)
    {
        if (preg_match('/^[1-9][0-9]{3}\s?[a-z|A-Z]{2}$/', $postcode)) // 4 cijfers, 0 of 1 spatie, 2 letters
            return true;
        else
            return $this->invalidate($postcode, ' is not a valid postcode.');
    }
    
    /**
     * check if given value is less than test value (numeric or alphabetically)
     * @param mixed $value
     * @param mixed $test
     */
    public function lessThan($value, $test)
    {
        if (! is_numeric($value) || ! is_numeric($test))
        {
            $value = ord($value); // implicit check for objects or arrays
            $test = ord($test);
        }
        
        if ($value < $test)
            return true;
        else
            return $this->invalidate($value, " is not more than $test.");
    }
    
    /**
     * check if given value is higher than test value (numeric or alphabetically)
     * @param mixed $value
     * @param mixed $test
     */
    public function moreThan($value, $test)
    {
        if (! is_numeric($value) || ! is_numeric($test))
        {
            $value = ord($value); // implicit check for objects or arrays
            $test = ord($test);
        }
        
        if ($value > $test)
            return true;
        else
            return $this->invalidate($value, " is not more than $test.");
    }
    
    /**
     * check if given value is between start and end values (numeric or alphabetically)
     * @param mixed $value
     * @param mixed $test
     */
    public function between($value, $start, $end)
    {
        // store for invalidate message if required
        $origStart = $start;
        $origEnd = $end;
        
        if (! is_numeric($value) || ! is_numeric($start) || ! is_numeric($end))
        {
            $value = ord($value); // implicit check for objects or arrays
            $start = ord($start);
            $end = ord($end);
        }
        
        if ($value >= $start && $value <= $end)
            return true;
        else
            return $this->invalidate($value, " is not between $origStart and $origEnd");
    }
    
    /**
     * check if given $id is a valid id in html
     * @param string $id
     */
    public function htmlId($id)
    {
        if (preg_match('/(^[a-z]{1}[a-z0-9_\-\.\:]+)$/i', $id))
            return true;
        else
            return $this->invalidate($id, " is not a valid html id or name.");
    }
    
    /**
     * check that given value is not an array
     * @param array $value
     */
    public function isArray($value)
    {
        if (is_array($value))
            return true;
        else
            return $this->invalidate($value, "Given value has to be an array.");
    }
    
    /**
     * check that given value is not an array
     * @param array $value
     */
    public function isNotArray($value)
    {
        if (! is_array($value))
            return true;
        else
            return $this->invalidate($value, "Array given where not expected.");
    }
    
    /**
     * check that $value is in allowed values of $array
     * Synonym of $this->inArray()
     * @param string $value
     * @param array $array
     */
    public function allowedValue($value, $array)
    {
        return $this->inArray($value, $array);
    }
    
    /**
     * check that $value is in allowed values of $array
     * @param string $value
     * @param array $array
     */
    public function inArray($value, $array)
    {
        if (! is_array($array))
            $array = array($array);
        
        // create array representation string
        $count = count($array);
        if ($count > 5)
            $arrayToString = $array[0] . ', ' . $array[1] . ', ' . $array[2] . ' ... ' . $array[$count - 1] . ' ('.$count.' options)';
        else
            $arrayToString = implode(', ', $array);
        
        if (empty($value))
        {
            // compare strict for anything that converts to empty  (null != 0)
            if (in_array($value, $array, true))
                return true;
            else
                return $this->invalidate($value, " was not in array of allowed values: ".$arrayToString);
        }
        else
        {
            // compare loosely for the rest (1 == '1')
            if (in_array($value, $array))
                return true;
            else
                return $this->invalidate($value, " was not in array of allowed values: ".$arrayToString);
        }
    }
    
    /**
     * check if array has only unique values
     * @param array $array
     * @return boolean
     */
    public function hasUniqueValues($array)
    {
        if (empty($array) || ! is_array($array))
            return false;
        
        $unique = array_unique($array);
        if (count($unique) == count($array))
            return true;
        else
            return $this->invalidate($array, "Not all values in array are unique");
    }
    
    /**
     * check that given value is not empty
     * @param mixed $value
     */
    public function isNotEmpty($value)
    {
        if (! empty($value))
            return true;
        else
            return $this->invalidate($value, "Given value is empty.");
    }
    
    /**
     * check that given value is a boolean
     * @param bool $value
     */
    public function isBoolean($value)
    {
        if (is_bool($value))
            return true;
        else
            return $this->invalidate($value, "Given value is not a boolean.");
    }
    
    /**
     * check that $var is objects of $class
     * @param object | array of objects $var
     * @param string $class
     */
    public function isInstanceOf($var, $class)
    {
        // for the invalidate message we need to have class as string. Instanceof actually accepts both string and object
        if (is_object($class))
            $class = get_class($class);
         
        if (empty($class) || ! is_string($class))
            return $this->invalidate($class, ' is not a class to check for');
        
        if (is_array($var))
        {
            $errors = array();
            foreach ($var as $i => $val)
            {
                if (! $val instanceof $class)
                    $errors[] = "object of ".get_class($val)." at index $i is not an object of $class";
            }
            
            if (empty($errors))
                return true;
            else
                return $this->invalidate($var, implode("<br/>\n", $errors));
        }
        else
        {
            if ($var instanceof $class)
                return true;
            else
                return $this->invalidate($var, " is not an object of $class");
        }
    }
    
    /**
     * check that $query is a Query and that it executes
     * @param Query $query
     */
    public function isQuery($query)
    {
        if ($this->isInstanceOf($query, 'Query'))
        {
            if (@$query->execute())
                return true;
            else
            {
                $message = "Could not execute query";
                if (defined('ACCOUNT_LEVEL') && ACCOUNT_LEVEL == 'ADMIN')
                    $message = $query->getError($message);
                    
                return $this->invalidate($query, $message);
            }
        }
    }
    
    /**
     * validate phone number
     * @param phone number $phonenumber
     * @return boolean
     */
    public function isMobileNumber($phonenumber)
    {
        $valid = false;
        if (preg_match('/^06[0-9]{8}$/', $phonenumber))
            $valid = true;
        elseif (preg_match('/^\+[0-9]{2}6[0-9]{8}$/', $phonenumber))
            $valid = true;
            
        if ($valid)
            return true;
        else
            return $this->invalidate($phonenumber, 'is an invalid phone number');
    }
    
    /**
     * validate that uploaded file is of correct file type
     * @param string $file (typically: $_FILES['uploaded_file']['tmp_name'])
     * @param array $mimeTypes
     * @return boolean
     */
    public function hasAllowedMimeType($filename, $mimeTypes)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = @finfo_file($finfo, $filename);
        
        if (empty($mimetype))
            return $this->invalidate($mimetype, 'is an invalid file type.');
        
        finfo_close($finfo);
        
        if (in_array($mimetype, $mimeTypes))
        {
            return true;
        }
        else
        {
            // if mimetype is unknown, pass the mimetype instead of the filename, because that will typically look like "C:\wamp\tmp\php8739.tmp"
            return $this->invalidate($mimetype, 'is an invalid file type.');
        }
    }
    
    /**
     * Run all validations given in the array. Values in the array have to exactly match method names of this Validate class.
     * @param mixed $value to validate
     * @param array $array validation methods
     * @return boolean
     */
    public function validateAll($value, $array)
    {
        $noValidationMethods = array('invalidate', 'reset', 'getMessage', 'isValid', 'validateAll', '__construct');
        $methods = array_diff(get_class_methods($this), $noValidationMethods);
        foreach ($array as $validation)
        {
            try
            {
                // if given $validation is a valid method of this class then run that method
                if (! in_array($validation, $methods))
                    throw new \Exception('Invalid validation asked: '.$validation);
                
                $r = new \ReflectionMethod($this, $validation);
                if (count($r->getParameters()) > 1)
                    throw new \Exception('Cannot perform validation '.$validation.', because more than just value parameter is required.');
                
                $this->$validation($value);
            }
            catch (\Exception $e)
            {
                echo error($e->getMessage());
            }
        }
        
        return $this->valid;
    }
    
    /**
     * getter for valid property
     * returns false if any validation on the page did not pass
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }
    
    /**
     * return the found error messages optionally formatted in error divs
     */
    public function getMessage($message = null, $inErrorDiv = true)
    {
        $message = ! empty($message) ? '<p>'.$message."</p>\n" : "";
        return $inErrorDiv ? '<div class="error">'.$message.'<p>'.$this->message."</p></div>\n" : $message.$this->message;
    }
    
    /**
     * reset validator (so you don't have to create a new object for each use if you don't want to)
     */
    public function reset()
    {
        $this->valid = true;
        $this->message = '';
    }
    
    
    /**
     * function to handle found invalidities
     * public, so it is possible to use this class with custom validation
     *
     * @param string $message
     * @return boolean
     */
    public function invalidate($var, $message)
    {
        $this->valid = false;  // invalidate the posted vars
        
        if (empty($var))
            $this->message .= "''";
        elseif (is_object($var))
            $this->message .= 'class ' . get_class($var);
        elseif (is_array($var))
            $this->message .= 'array';
        else
            $this->message .= xsschars($var);
        
        $this->message .= ' '.$message."<br/>\n";
        
        return false;  // always return false
    }
    
}
