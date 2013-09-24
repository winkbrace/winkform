<?php namespace WinkBrace\WinkForm\Input;

class EmailInput extends Input
{
    protected $type = 'email';
    

    /**
     * render the text input element
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
                . $this->renderMaxLength()
                . $this->renderTitle()
                . $this->renderDataAttributes()
                . $this->renderRequired()
                . $this->renderPlaceholder()
                .' />'."\n";
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
}
