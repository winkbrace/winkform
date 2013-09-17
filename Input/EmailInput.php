<?php namespace WinkForm\Input;

class EmailInput extends Input
{
    protected $type = 'email';
    

    /**
     * render the text input element
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
