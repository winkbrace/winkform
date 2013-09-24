<?php namespace WinkBrace\WinkForm\Input;

class TextInput extends Input
{
    
    protected $type = 'text',
              $maxLength;

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
                . $this->renderAutoFocus()
                .' />'."\n";
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
    /**
     * @return int $maxLength
     */
    public function renderMaxLength()
    {
        return ! empty($this->maxLength) ? ' maxlength="'.$this->maxLength.'"' : '';
    }

    /**
     * @param int $maxLength
     */
    public function setMaxLength($maxLength)
    {
        if (! $this->validator->validate($maxLength, 'numeric'))
            throw new \Exception('Invalid value for maxLength: '.$maxLength);
        else
            $this->maxLength = $maxLength;
        
        return $this;
    }

}
