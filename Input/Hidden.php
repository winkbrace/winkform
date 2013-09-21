<?php namespace WinkBrace\WinkForm\Input;

class Hidden extends Input
{
    protected $type = 'hidden';
    

    /**
     * render the hidden input element
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        $output = '<input'
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
                .' />'."\n";
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
}
