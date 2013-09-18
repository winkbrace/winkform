<?php namespace WinkForm\Input;

class Custom extends Input
{

    /**
     * The type of the <input> element to display.
     * The default type is: 'text'.
     * @var string
     */
    protected $type = 'text';
    

    /**
     * render the custom input element
     */
    public function render()
    {
        // default validity check
        if (! $this->validator->isValid())
            throw new \Exception($this->validator->getMessage('Error rendering '.get_class($this).' object with name '.$this->name));
            
        $output = $this->renderLabel()
            . '<input'
            . $this->renderType()
            . $this->renderId()
            . $this->renderClass()
            . $this->renderName()
            . $this->renderValue()
            . $this->renderStyle()
            . $this->renderDisabled()
            . $this->renderTitle()
            . $this->renderDataAttributes()
            . $this->renderRequired()
            . $this->renderPlaceholder()
            . $this->renderAutoFocus()
            . ' />'
            . PHP_EOL;
        
        $output .= $this->renderInvalidations();
            
        return $output;
    }
    
    /**
     * @link http://www.w3schools.com/tags/att_input_type.asp
     * @param string $type
     */
    public function setType($type)
    {
        if (empty($type))
            return;
        
        // there are many already defined types, we only want to cover the other
        // ones that don't have a custom implementation
        $allowedTypes = array(
            'button'         => array('Button.php'),
            'checkbox'       => array('Checkbox.php'),
            'color'          => null,
            'date'           => array('DateTinput.php', 'DateRange.php'),
            'datetime'       => array('DateTinput.php', 'DateRange.php'),
            'datetime-local' => array('DateTinput.php', 'DateRange.php'),
            'email'          => null,
            'file'           => array('FileInput.php'),
            'hidden'         => array('HiddenInput.php'),
            'image'          => null,
            'month'          => array('MonthInput.php', 'MonthRange.php'),
            'number'         => null,
            'password'       => array('PasswordInput.php'),
            'radio'          => array('RadioInput.php'),
            'range'          => null,
            'reset'          => null,
            'search'         => null,
            'submit'         => array('SubmitButton.php'),
            'tel'            => null,
            'text'           => array('TextInput.php'),
            'time'           => array('DateTinput.php', 'DateRange.php'),
            'url'            => null,
            'week'           => array('WeekInput.php', 'WeekRange.php'),
        );
        
        // Check if he selected an input type that exists
        if (! array_key_exists($type, $allowedTypes))
            throw new \Exception('The type specified for this input field '. $type .' is not a valid one.
                            Please select one of the following types: ' . implode(', ', array_keys($allowedTypes)));
        
        // Check if we don't have already defined a class for this
        if (! is_null($allowedTypes[$type]))
            throw new \Exception('It looks like the type: ' . $type . ' already has a better implementation.
                            You should try this first: ' . implode(', ', $allowedTypes[$type]));
        
        // This will be the <input> element type
        $this->type = $type;
    }
    
}
