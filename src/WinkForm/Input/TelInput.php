<?php namespace WinkForm\Input;

class TelInput extends Input
{

    protected $type = 'tel';

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
                . $this->renderTitle()
                . $this->renderDataAttributes()
                . $this->renderRequired()
                . $this->renderPlaceholder()
                . $this->renderAutoFocus()
                .' />'."\n";

        $output .= $this->renderInvalidations();

        return $output;
    }

}
