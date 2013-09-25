<?php namespace WinkForm\Input;

class CustomInput extends Input
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
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

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
            'date'           => array('DateInput.php', 'DateRangeInput.php'),
            'datetime'       => array('DateInput.php', 'DateRangeInput.php'),
            'datetime-local' => array('DateInput.php', 'DateRangeInput.php'),
            'email'          => array('EmailInput.php'),
            'file'           => array('FileInput.php'),
            'hidden'         => array('HiddenInput.php'),
            'image'          => array('ImageButton.php'),
            'month'          => array('MonthInput.php', 'MonthRangeInput.php'),
            'number'         => null,
            'password'       => array('PasswordInput.php'),
            'radio'          => array('RadioInput.php'),
            'range'          => null,
            'reset'          => array('ResetButton.php'),
            'search'         => null,
            'submit'         => array('SubmitButton.php'),
            'tel'            => null,
            'text'           => array('TextInput.php'),
            'time'           => array('DateInput.php', 'DateRangeInput.php'),
            'url'            => null,
            'week'           => array('WeekInput.php', 'WeekRangeInput.php'),
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
