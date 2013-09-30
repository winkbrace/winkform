<?php namespace WinkForm\Button;

class InputButton extends \WinkForm\Input\Input
{
    protected $type = 'button';
    
    
    /**
     * construct InputButton
     * @param string $name
     * @param mixed $value
     */
    function __construct($name, $value = null)
    {
        parent::__construct($name, $value);
    
        // always set btn class
        $this->addClass('btn');
    }
    
    /**
     * render the button
     */
    public function render()
    {
        // default validity check
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
                . $this->renderAutoFocus()
                .' />'."\n";
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
}
