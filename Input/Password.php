<?php namespace WinkForm\Input;

class Password extends Input
{

    protected $type = 'password',
              $maxLength;

    
    /**
     * render the hidden input element
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
                . ' value=""'
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
     * @return html maxlength="$maxLength"
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
        if (! $this->validator->numeric($maxLength))
            throw new \Exception('Invalid value for maxLength: '.$maxLength);
        else
            $this->maxLength = $maxLength;
        
        return $this;
    }

}
