<?php namespace WinkForm\Input;

class ResetButton extends Input
{
    protected $type = 'reset';
    

    /**
     * construct SubmitButton
     * @param string $name
     * @param mixed optional $value
     */
    function __construct($name, $value = null)
    {
        parent::__construct($name, $value);
        
        // always set btn class
        $this->addClass('btn');
    }

    /**
     * render the reset button
     */
    public function render()
    {
        // default validity check
        if (! $this->validate->isValid())
            throw new FormException($this->validate->getMessage('Error rendering '.get_class($this).' object with name '.$this->name));
        
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
                . $this->renderAutoFocus()
                .' />'."\n";
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
}
