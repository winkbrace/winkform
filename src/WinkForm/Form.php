<?php namespace WinkForm;

use WinkForm\Input\DateRangeInput;

use WinkForm\Input\DateInput;

/**
 * abstract class Form
 *
 * @author Bas
 *
 */
abstract class Form
{
    const ENCTYPE_DEFAULT = 'application/x-www-form-urlencoded';
    const ENCTYPE_FILE    = 'multipart/form-data';


    /**
     * create AddressInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\AddressInput
     */
    public static function address($name, $value = null)
    {
        return new Input\AddressInput($name, $value);
    }

    /**
     * create Button object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Button\InputButton
     */
    public static function inputButton($name, $value = null)
    {
        return new Button\InputButton($name, $value);
    }

    /**
     * create ChainedDropdowns object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\ChainedDropdowns
     */
    public static function chainedDropdowns($name, $value = null)
    {
        return new Input\ChainedDropdowns($name, $value);
    }

    /**
     * create Checkbox object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\Checkbox
     */
    public static function checkbox($name, $value = null)
    {
        return new Input\Checkbox($name, $value);
    }

    /**
     * create ColorInput object
     * @param string $name
     * @param string $value
     * @return Input\ColorInput
     */
    public static function color($name, $value = null)
    {
        return new Input\ColorInput($name, $value);
    }

    /**
     * create DateInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\DateInput
     */
    public static function date($name, $value = null)
    {
        return new Input\DateInput($name, $value);
    }

    /**
     * create DateRange object
     * @param string $name
     * @param dd-mm-yyyy $from
     * @param dd-mm-yyyy $to
     * @return \WinkForm\Input\DateRangeInput
     */
    public static function dateRange($name, $from, $to)
    {
        return new Input\DateRangeInput($name, $from, $to);
    }

    /**
     * create Dropdown object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\Dropdown
     */
    public static function dropdown($name, $value = null)
    {
        return new Input\Dropdown($name, $value);
    }

    /**
     * create Email object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\EmailInput
     */
    public static function email($name, $value = null)
    {
        return new Input\EmailInput($name, $value);
    }

    /**
     * create FileInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\FileInput
     */
    public static function file($name, $value = null)
    {
        return new Input\FileInput($name, $value);
    }

    /**
     * create HiddenInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\HiddenInput
     */
    public static function hidden($name, $value = null)
    {
        return new Input\HiddenInput($name, $value);
    }

    /**
     * create ImageButton object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Button\ImageButton
     */
    public static function image($name, $value = null)
    {
        return new Button\ImageButton($name, $value);
    }

    /**
     * create MonthInput object
     * @param string $name
     * @param yyyy-mm $month
     * @return \WinkForm\Input\MonthInput
     */
    public static function month($name, $month = null)
    {
        return new Input\MonthInput($name, $month);
    }

    /**
     * create MonthRange object
     * @param string $name
     * @param yyyy-mm $from
     * @param yyyy-mm $to
     * @return \WinkForm\Input\MonthRangeInput
     */
    public static function monthRange($name, $from = null, $to = null)
    {
        return new Input\MonthRangeInput($name, $from, $to);
    }

    /**
     * create NumberInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\NumberInput
     */
    public static function number($name, $value = null)
    {
        return new Input\NumberInput($name, $value);
    }

    /**
     * create PasswordInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\PasswordInput
     */
    public static function password($name, $value = null)
    {
        return new Input\PasswordInput($name, $value);
    }

    /**
     * create RadioInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\RadioInput
     */
    public static function radio($name, $value = null)
    {
        return new Input\RadioInput($name, $value);
    }
    
    /**
     * create a <button> element
     * @param string $name
     * @param string $value
     * @return \WinkForm\Button\Button
     */
    public static function button($name, $value = null)
    {
        return new Button\Button($name, $value);
    }

    /**
     * create RangeInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\RangeInput
     */
    public static function range($name, $value = null)
    {
        return new Input\RangeInput($name, $value);
    }

    /**
     * create reset button
     * @param string $name
     * @param string $value
     * @return \WinkForm\Button\ResetButton
     */
    public static function reset($name, $value = null)
    {
        return new Button\ResetButton($name, $value);
    }

    /**
     * create SearchInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\SearchInput
     */
    public static function search($name, $value = null)
    {
        return new Input\SearchInput($name, $value);
    }

    /**
     * create SubmitButton object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Button\SubmitButton
     */
    public static function submit($name, $value = null)
    {
        return new Button\SubmitButton($name, $value);
    }

    /**
     * create TelInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\TelInput
     */
    public static function tel($name, $value = null)
    {
        return new Input\TelInput($name, $value);
    }

    /**
     * create TextInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\TextInput
     */
    public static function text($name, $value = null)
    {
        return new Input\TextInput($name, $value);
    }

    /**
     * create TextAreaInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\TextAreaInput
     */
    public static function textarea($name, $value = null)
    {
        return new Input\TextAreaInput($name, $value);
    }

    /**
     * create UrlInput object
     * @param string $name
     * @param string $value
     * @return \WinkForm\Input\UrlInput
     */
    public static function url($name, $value = null)
    {
        return new Input\UrlInput($name, $value);
    }

    /**
     * create WeekInput object
     * @param string $name
     * @param iyyy-iw $week
     * @return \WinkForm\Input\WeekInput
     */
    public static function week($name, $week = null)
    {
        return new Input\WeekInput($name, $week);
    }

    /**
     * create WeekRange object
     * @param string $name
     * @param iyyy-iw $from
     * @param iyyy-iw $to
     * @return \WinkForm\Input\WeekRangeInput
     */
    public static function weekRange($name, $from = null, $to = null)
    {
        return new Input\WeekRangeInput($name, $from, $to);
    }







    protected $method = 'post',
              $action = '',
              $enctype = self::ENCTYPE_DEFAULT,
              $name,
              $isValid = true,
              $validator;   // Validate class to perform the POST validation


    /**
     * Create new Form
     */
    public function __construct()
    {
        $this->validator = new \WinkForm\Validation\FormValidator();
    }

    /**
     * render the form
     * @return string
     */
    abstract public function render();

    /**
     * render the default form open tag
     * @return string
     */
    protected function renderFormHead()
    {
        $this->determineEnctype();
        return '<form name="'.$this->name.'" method="'.$this->method.'" action="'.$this->action.'" enctype="'.$this->enctype.'">'."\n";
    }

    /**
     * render the form close tag
     * @return string
     */
    protected function renderFormFoot()
    {
        return '</form>'."\n";
    }

    /**
     * validate all posted values for the form input fields
     * @return boolean
     */
    public function validate()
    {
        // handle validations passed to this form or to the public input fields
        foreach (get_object_vars($this) as $input)
        {
            if (! $input instanceof Input\Input)
                continue;

            $this->validateInput($input);
        }

        return $this->isValid();
    }

    /**
     * validate a single input object using default validations or
     * custom validations assigned to the object or to the form
     * @param \WinkForm\Input\Input $input
     */
    protected function validateInput(\WinkForm\Input\Input $input)
    {
        // skip non-required fields that are not posted
        if (! $input->isPosted() && ! $input->isRequired())
            return;

        // always validate that posted value(s) of checkbox, radio or dropdown are in the array of values of the Input element
        $values = $input->getValues();
        if (! empty($values))
        {
            $this->validator->addValidation($input, 'all_in:'.implode(',', $values));
        }
        
        // always validate the date
        if ($input instanceof DateInput)
        {
            $this->validator->addValidation($input, 'date_format:'.$input->getDateFormat());
        }
        elseif ($input instanceof DateRangeInput)
        {
            $this->validator->addValidation($input->getDateFrom(), 'date_format:'.$input->getDateFrom()->getDateFormat());
            $this->validator->addValidation($input->getDateTo(), 'date_format:'.$input->getDateTo()->getDateFormat());
        }

        // validations added to the Input element
        if ($input->hasValidations())
        {
            $this->validator->addValidation($input, $input->getValidations());
        }

        // place found invalidations after the input element
        if (! $this->validator->isValid())
        {
            $this->invalidate($input, implode("<br/>\n", $this->validator->getAttributeErrors($input->getName())));
        }

        // clear the validator for the next input
        $this->validator->reset();
    }

    /**
     * is the form posted?
     * @return boolean
     */
    abstract public function isPosted();

    /**
     * Invalidate input field (and let this form know it is invalid)
     * @param Input\Input $input
     * @param string $invalidation
     */
    public function invalidate(Input\Input $input, $invalidation)
    {
        $this->isValid = false;
        $input->addInvalidation($invalidation);
    }

    /**
     * generate salt
     * @param int $length
     * @return string
     */
    public function generateSalt($length = 10)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $salt = '';
        for ($i = 0; $i < $length; $i++)
            $salt .= $chars[mt_rand(0, strlen($chars) - 1)];

        return $salt;
    }

    /**
     * is the form valid?
     * @return boolean
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @return string $method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string $action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string $enctype
     */
    public function getEnctype()
    {
        return $this->enctype;
    }

    /**
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        if (! in_array($method, array('post', 'get')))
            throw new \Exception('Invalid method for Form');

        $this->method = $method;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param string $enctype
     */
    public function setEnctype($enctype)
    {
        if (! in_array($enctype, array(self::ENCTYPE_DEFAULT, self::ENCTYPE_FILE, 'text/plain')))
            throw new \Exception('Invalid enctype given for Form');

        $this->enctype = $enctype;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * check if the Form has FileInput objects and if so, set the enctype to Form::ENCTYPE_FILE
     */
    protected function determineEnctype()
    {
        // check the properties
        foreach (get_object_vars($this) as $input)
        {
            if (is_object($input) && $input instanceof Input\FileInput)
            {
                $this->setEnctype(self::ENCTYPE_FILE);
                return $this->enctype; // immediately quit searching when a FileInput is found
            }
        }

        return $this->enctype;
    }

}
