<?php namespace WinkBrace\WinkForm;

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
     * @return \WinkBrace\WinkForm\Input\AddressInput
     */
    public static function address($name, $value = null)
    {
        return new Input\AddressInput($name, $value);
    }
    
    /**
     * create Button object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Button\Button
     */
    public static function button($name, $value = null)
    {
        return new Button\Button($name, $value);
    }
    
    /**
     * create ChainedDropdowns object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\ChainedDropdowns
     */
    public static function chainedDropdowns($name, $value = null)
    {
        return new Input\ChainedDropdowns($name, $value);
    }
    
    /**
     * create Checkbox object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\Checkbox
     */
    public static function checkbox($name, $value = null)
    {
        return new Input\Checkbox($name, $value);
    }
    
    /**
     * create custom Input element
     * @link http://www.w3schools.com/tags/att_input_type.asp
     * @param string $type
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\CustomInput
     */
    public static function custom($type, $name, $value = null)
    {
        $custom = new Input\CustomInput($name, $value);
        $custom->setType($type);
        return $custom;
    }
    
    /**
     * create DateInput object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\DateInput
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
     * @return \WinkBrace\WinkForm\Input\DateRangeInput
     */
    public static function dateRange($name, $from, $to)
    {
        return new Input\DateRangeInput($name, $from, $to);
    }
    
    /**
     * create Dropdown object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\Dropdown
     */
    public static function dropdown($name, $value = null)
    {
        return new Input\Dropdown($name, $value);
    }
    
    /**
     * create Email object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\EmailInput
     */
    public static function email($name, $value = null)
    {
        return new Input\EmailInput($name, $value);
    }
    
    /**
     * create FileInput object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\FileInput
     */
    public static function file($name, $value = null)
    {
        return new Input\FileInput($name, $value);
    }
    
    /**
     * create HiddenInput object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\HiddenInput
     */
    public static function hidden($name, $value = null)
    {
        return new Input\HiddenInput($name, $value);
    }
    
    /**
     * create ImageButton object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Button\ImageButton
     */
    public static function image($name, $value = null)
    {
        return new Button\ImageButton($name, $value);
    }
    
    /**
     * create MonthInput object
     * @param string $name
     * @param yyyy-mm $month
     * @return \WinkBrace\WinkForm\Input\MonthInput
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
     * @return \WinkBrace\WinkForm\Input\MonthRangeInput
     */
    public static function monthRange($name, $from = null, $to = null)
    {
        return new Input\MonthRangeInput($name, $from, $to);
    }
    
    /**
     * create PasswordInput object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\PasswordInput
     */
    public static function password($name, $value = null)
    {
        return new Input\PasswordInput($name, $value);
    }
    
    /**
     * create RadioInput object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\RadioInput
     */
    public static function radio($name, $value = null)
    {
        return new Input\RadioInput($name, $value);
    }
    
    /**
     * create reset button
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Button\ResetButton
     */
    public static function reset($name, $value = null)
    {
        return new Button\ResetButton($name, $value);
    }
    
    /**
     * create SubmitButton object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Button\SubmitButton
     */
    public static function submit($name, $value = null)
    {
        return new Button\SubmitButton($name, $value);
    }
    
    /**
     * create TextInput object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\TextInput
     */
    public static function text($name, $value = null)
    {
        return new Input\TextInput($name, $value);
    }
    
    /**
     * create TextAreaInput object
     * @param string $name
     * @param string $value
     * @return \WinkBrace\WinkForm\Input\TextAreaInput
     */
    public static function textarea($name, $value = null)
    {
        return new Input\TextAreaInput($name, $value);
    }
    
    /**
     * create WeekInput object
     * @param string $name
     * @param iyyy-iw $week
     * @return \WinkBrace\WinkForm\Input\WeekInput
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
     * @return \WinkBrace\WinkForm\Input\WeekRangeInput
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
              $validator,   // Validate class
              $validations = array(); // array of custom validations on the input fields
    
    
    /**
     * Create new Form
     */
    public function __construct()
    {
        $this->validator = new \WinkBrace\WinkForm\Validation\Validator();
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
     * validate all form input fields
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
     * @param Input\Input $input
     */
    protected function validateInput(Input\Input $input)
    {
        // validate required fields
        if ($input->isRequired() && ! $input->isPosted())
            $this->invalidate($input, 'This is a required field.');
        
        // skip non-required fields that are not posted
        if (! $input->isPosted())
            return;
        
        // always validate date inputs
        if ($input instanceof Input\DateInput)
            $this->validator->date($input->getPosted());
        
        if ($input instanceof Input\DateRangeInput)
        {
            $this->validator->date($input->getDateFrom()->getPosted());
            $this->validator->date($input->getDateTo()->getPosted());
        }
        
        // always validate that posted value of checkbox, radio or dropdown is one of the supplied values
        $values = $input->getValues();
        if (! empty($values))
        {
            $posted = $input->getPosted();
            if (is_array($posted)) // checkboxes and dropdowns allow multiple values to be posted
            {
                foreach ($posted as $post)
                    $this->validator->inArray($post, $values);
            }
            else
            {
                $this->validator->inArray($posted, $values);
            }
        }
        
        // custom validations added to Input
        if ($input->hasValidations())
        {
            foreach ($input->getValidations() as $validation)
            {
                // if an array is given as an argument, we need to write it out as an array creation before passing it to eval() as string
                for ($i = 0; $i < count($validation['parameters']); $i++)
                {
                    if (is_array($validation['parameters'][$i]))
                        $validation['parameters'][$i] = 'array("' . implode('", "', $validation['parameters'][$i]) . '")';
                }
                
                $pars = ! empty($validation['parameters']) ? ', ' . implode(', ', $validation['parameters']) : '';
                eval('$this->validator->' . $validation['validation'] . '($input->getPosted()' . $pars . ');');
            }
        }
        
        // custom validations added to this Form
        if (array_key_exists($input->getName(), $this->validations))
        {
            // multiple validations per input possible
            foreach ($this->validations[$input->getName()] as $validation)
            {
                // if an array is given as an argument, we need to write it out as an array creation before passing it to eval() as string
                for ($i = 0; $i < count($validation['parameters']); $i++)
                {
                    if (is_array($validation['parameters'][$i]))
                        $validation['parameters'][$i] = 'array("' . implode('", "', $validation['parameters'][$i]) . '")';
                }
                
                $pars = ! empty($validation['parameters']) ? ', ' . implode(', ', $validation['parameters']) : '';
                eval('$this->validator->' . $validation['validation'] . '($input->getPosted()' . $pars . ');');
            }
        }
        
        // place found invalidations after the input element
        if (! $this->validator->passes())
            $this->invalidate($input, $this->validator->getMessage('', false));
        
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
     * add validation for public input fields
     * Example: $form->addValidation('arpu', 'between', array(20, 30))
     * @param string|Input\Input $input  input name or Input object
     * @param string $validation (must be method of Validate class!)
     * @param array $parameters
     */
    public function addValidation($input, $validation, $parameters = array())
    {
        if ($input instanceof Input\Input)
            $inputName = $input->getName();
        elseif (is_string($input))
            $inputName = $input;
        else
            throw new \Exception('Invalid $input given to add validation to Form');
    
        // validate validation exists in Validate (teehee)
        if (! method_exists($this->validator, $validation))
            throw new \Exception('The validation '.$validation.' does not exist in class Validate');
        
        // if developer forgets that parameters have to be in an array (when there is only 1 value for example) then
        // be lenient and put the parameter in an array here
        if (! is_array($parameters))
            $parameters = array($parameters);
    
        $this->validations[$inputName][] = array('validation' => $validation, 'parameters' => $parameters);
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
        if ($this->validator->inArray($enctype, array(self::ENCTYPE_DEFAULT, self::ENCTYPE_FILE, 'text/plain')))
        {
            $this->enctype = $enctype;
        }
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
