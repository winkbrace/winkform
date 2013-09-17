<?php namespace WinkForm\Button;

class Image extends Button
{
    
    protected $type = 'image',
              $src,
              $alt;
    
    
    /**
     * construct Button
     * @param string $name
     * @param mixed optional $value
     */
    function __construct($name, $value = null)
    {
        parent::__construct($name, $value);
    
        // remove default bootstrapper btn class
        $this->removeClass('btn');
    }

    /**
     * render the image button
     */
    public function render()
    {
        // default validity check
        $this->validate->isNotEmpty($this->src);
        if (! $this->validate->isValid())
            throw new \Exception($this->validate->getMessage('Error rendering '.get_class($this).' object with name '.$this->name));
        
        $output = $this->renderLabel()
                . '<input'
                . $this->renderType()
                . $this->renderSrc()
                . $this->renderAlt()
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
    
    /**
     * @return $src
     */
    public function renderSrc()
    {
        return ' src="'.$this->src.'"';
    }

    /**
     * @param url $src
     */
    public function setSrc($src)
    {
        if ($this->validate->isValidUrl($src))
        {
            $this->src = $src;
        }
        
        return $this;
    }
    
    /**
     * @return string $alt
     */
    public function renderAlt()
    {
        return ' alt="'.$this->alt.'"';
    }

    /**
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        
        return $this;
    }
    
}
