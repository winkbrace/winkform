<?php namespace WinkBrace\WinkForm\Validation;

/**
 *
 * @author b-deruiter
 *
 */
class ExtendedValidator extends \Illuminate\Validation\Validator
{
    /**
     * Validate that an attribute is not an array.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateNotArray($attribute, $value)
    {
        return ! is_array($value);
    }
    
    /**
     * Validate that an attribute is boolean
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateBoolean($attribute, $value)
    {
        return is_bool($value);
    }
    
    /**
     * Validate that an attribute is a numeric array
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateNumericArray($attribute, $value)
    {
        if (! is_array($value))
            return false;
        
        if (empty($value))
            return true;
        
        return $this->arrayIsNumeric($value);
    }
    
    /**
     * Validate that an attribute is an assoc array
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAssocArray($attribute, $value)
    {
        if (! is_array($value))
            return false;
        
        if (empty($value))
            return true;
        
        return ! $this->arrayIsNumeric($value);
    }
    
    /**
     * private method to be used by validateNumericArray and validateAssocArray
     * @param array $array
     * @return boolean
     */
    protected function arrayIsNumeric(array $array)
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
     * validate that value is not empty
     * @param string $attribute
     * @param mixed $value
     * @return boolean
     */
    protected function validateNotEmpty($attribute, $value)
    {
        return ! empty($value);
    }
    
    /**
     * validate that value is empty
     * @param string $attribute
     * @param mixed $value
     * @return boolean
     */
    protected function validateEmpty($attribute, $value)
    {
        return empty($value);
    }
    
}
