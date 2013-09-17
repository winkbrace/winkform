<?php namespace WinkForm\Input;

/**
 * class to create a <input type="file">
 * Don't forget to set the enctype of the form in order to actually upload the file!!
 * enctype="multipart/form-data"
 *
 */
class File extends Input
{
    protected $type = 'file';
    
    /**
     * render the hidden input element
     */
    public function render()
    {
        // default validity check
        if (! $this->validate->isValid())
            throw new \Exception($this->validate->getMessage('Error rendering '.get_class($this).' object with name '.$this->name));
        
        // default "width" (actually invalid html, but it will work)
        if (empty($this->size))
            $this->size = 40;
            
        $output = $this->renderLabel()
            . '<input'
            . $this->renderType()
            . $this->renderId()
            . $this->renderClass()
            . $this->renderName()
            . $this->renderStyle()
            . $this->renderDisabled()
            . $this->renderTitle()
            . $this->renderDataAttributes()
            . $this->renderRequired()
            . $this->renderAutoFocus()
            . ' />'
            . PHP_EOL;
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
    /**
     * get contents of uploaded file
     * @return boolean|string $contents
     */
    public function getContents()
    {
        if (! $this->isPosted())
            return false;
        
        $contents = file_get_contents($this->posted);
        return mb_convert_encoding($contents, 'UTF-8');
    }
    
    /**
     * get content of uploaded file as array of lines
     * @return array $lines
     */
    public function getLines()
    {
        if (! $this->isPosted())
            return false;
        
        $lines = file($this->posted, FILE_IGNORE_NEW_LINES);
        for ($i = 0; $i < count($lines); $i++)
        {
            $lines[$i] = mb_convert_encoding($lines[$i], 'UTF-8');
        }
        
        return $lines;
    }
    
}
