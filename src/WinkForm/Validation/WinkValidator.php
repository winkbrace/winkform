<?php namespace WinkForm\Validation;

use \Illuminate\Validation\Validator;
use \Illuminate\Support\Collection;
use Symfony\Component\Translation\TranslatorInterface;

// TODO add error messages for these validations and ideally register them in this class

/**
 * Extension on Laravel Validator class with extra validations
 * @author b-deruiter
 */
class WinkValidator extends Validator
{
    /**
     * Create a new Validator instance.
     *
     * @param  \Symfony\Component\Translation\TranslatorInterface  $translator
     * @param  array $data
     * @param  array $rules
     * @param  array $messages
     * @param  array $customAttributes
     * @return \WinkForm\Validation\WinkValidator
     */
    public function __construct(TranslatorInterface $translator, $data, $rules, $messages = array(), $customAttributes = array())
    {
        // add NotEmpty to the implicit exceptions to skipping empty string validations
        $this->implicitRules[] = 'NotEmpty';

        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
    }

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

    /**
     * validate that all values in array are in given array
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return bool
     */
    protected function validateAllIn($attribute, $value, $parameters)
    {
        // all_in should also be able to handle single posted values like a normal in.
        if (! is_array($value))
            return in_array($value, $parameters);

        if (empty($value))
            return true;

        foreach ($value as $val)
        {
            if (! in_array($val, $parameters))
                return false;
        }

        return true;
    }

    /**
     * validate that given value is a mobile phone number
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateMobileNumber($attribute, $value)
    {
        if (preg_match('/^06[0-9]{8}$/', $value))
            return true;

        if (preg_match('/^\+[0-9]{2}6[0-9]{8}$/', $value))
            return true;

        return false;
    }

    /**
     * validate that value is Dutch postcode
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validatePostcode($attribute, $value)
    {
        if (preg_match('/^[1-9][0-9]{3}\s?[a-z|A-Z]{2}$/', $value)) // 4 cijfers, 0 of 1 spatie, 2 letters
            return true;

        return false;
    }

    /**
     * Checks if the given date is greater than the minimum date
     *
     * @param string $attribute
     * @param string $value
     * @param string $parameters
     * @return boolean
     */
    protected function validateDateMin($attribute, $value, $parameters)
    {
        // when null there is no minimum
        if (is_null($parameters))
            return true;

        $dateToCheck = new \DateTime($value);
        $dateMin = new \DateTime('now');

        // number of days from today
        if (is_numeric($parameters))
        {
            $interval = \DateInterval::createFromDateString((int) $parameters . ' day');
            $dateMin->add($interval);
        }
        // relative date
        else
        {
            if (strtotime($parameters) === false)
                return false;

            $interval = \DateInterval::createFromDateString($parameters);
            $dateMin->add($interval);
        }

        // finally check to see if the date is bigger than the minimum
        if ($dateToCheck > $dateMin)
            return true;

        return false;
    }

    /**
     * Check that all items in the array or Collection are instanceof parameter
     * @param string $attribute
     * @param array|Collection $value
     * @param array $parameters
     * @return boolean
     */
    protected function validateCollectionOf($attribute, $value, $parameters)
    {
        if (empty($parameters))
            return false;

        if (! is_array($value) && ! $value instanceof Collection)
            $value = array($value);

        foreach ($parameters as $class)
        {
            foreach ($value as $val)
            {
                if (! $val instanceof $class)
                    return false;
            }
        }

        return true;
    }

    /**
     * Check that value equals given parameter
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    protected function validateEquals($attribute, $value, $parameters)
    {
        if (empty($parameters))
            return false;

        return $value == $parameters;
    }

}
