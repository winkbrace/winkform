<?php namespace WinkForm\Input;

class Date extends Input
{
    /**
     * Contains the options for the jQuery DatePicker widget
     * @var array
     */
    protected $jsOptions = array();
    
    protected $type = 'text'; // 'date' will only accept yyyy-mm-dd, which is not the format we use :'(
    

    /**
     * render the date input element
     */
    public function render()
    {
        $output = '';
        
        // default validity check
        if (! $this->validate->isValid())
            throw new \Exception($this->validate->getMessage('Error rendering '.get_class($this).' object with name '.$this->name));
            
        // we will show/hide the container div for the text field and the image and not the text field and the image themselves
        $this->removeStyle('display:none');
        $hidden = $this->getHidden() === true ? ' style="display:none;"' : '';
        
        // create TextInput object with all same properties as this DateInput object
        $text = new TextInput($this->name);
        copySharedAttributes($text, $this);
        
        // set default width if none was given
        if (strpos($this->renderStyle(), 'width') === false)
            $text->setWidth(80);
        
        $text->setMaxLength(10);
        
        $output = '<div id="'.$this->id.'-container"'.$hidden.' style="float: left;">'
                . $text->render()
                . '</div>' . PHP_EOL;
        
        if (empty($this->disabled))
        {
            $output .= $this->getJS() . PHP_EOL;
        }
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
    /**
     * The JS to initialize the jQuery UI DatePicker
     *
     * @see getJSOptions()
     * @return string the JS script
     */
    protected function getJS()
    {
        return '<script type="text/javascript">
                    $(document).ready(function()
                    {
                        var options = ' .  $this->getJSOptions() . ';
                        $("#'.$this->id.'").datepicker(options);
                    });
                </script>';
    }
    
    /**
     * Prepares the array of options for the jQuery UI DatePicker
     *
     * @link http://api.jqueryui.com/datepicker
     * @return string a HTML escaped JSON with the options for the DatePicker.
     */
    protected function getJSOptions()
    {
        $options = $this->jsOptions;
        
        //there will be no validation here, it's assumed the user has knowledge
        // of the possible arguments. Only some defaults will be provided
        
        // Merge in defaults.
        $options += array(
            'dateFormat'      => 'dd-mm-yy',
            'firstDay'        => 1,
            'showButtonPanel' => true,
            'showWeek'        => true,
            'changeMonth'     => true,
            'changeYear'      => true,
            'showOn'          => 'both',
            'buttonImage'     => (defined('BASE_URL') ? BASE_URL : '/') . 'images/helveticons/32x32/Calendar alt 32x32.png',
            'buttonImageOnly' => true,
            'buttonText'      => 'Pick a date',
        );
        
        $json = json_encode($options);

        return $json;
    }
    
    /**
     * Set extra parameters or overwrite default ones for the DatePicker.
     * @param array $options
     */
    public function setDatePickerOptions(array $options = array())
    {
        if ($this->validate->isNotEmpty($options) && $this->validate->isArray($options))
        {
            $this->jsOptions = $options;
        }
    }
    
}
