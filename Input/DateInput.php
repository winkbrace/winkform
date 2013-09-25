<?php namespace WinkForm\Input;

class DateInput extends Input
{
    /**
     * Contains the options for the jQuery DatePicker widget
     * @var array
     */
    protected $jsOptions = array();
    
    protected $type = 'text', // 'date' will only accept yyyy-mm-dd, which is not the format we use :'(
              $dateFormat,
              $dateFormatDelimiter;
    
    
    /**
     * construct DateInput
     * @param string $name
     * @param mixed $value
     */
    function __construct($name, $value = null)
    {
        parent::__construct($name, $value);
        
        // set date format
        $config = require WINKFORM_PATH.'config.php';
        $this->setDateFormat($config['date_format']);
    }

    /**
     * Override setPosted, to correct manually entered data that is lacking leading zeroes
     * @return $this
     */
    protected function setPosted()
    {
        if (! empty($_POST[$this->name]))
        {
            $post = xsschars($_POST[$this->name]);

            // This is a fix for when users manually input dates without using leading 0s
            $post = $this->getCorrectedPostedDate($post);

            $this->posted = $post;
            $this->selected = $post;  // so we can always retrieve the selected fields with getSelected()
        }

        return $this;
    }

    /**
     * This is a fix for when users manually input dates without using leading 0s
     * We can think of a lot more checks, but let's keep it as fast as possible. Real validation is in the validate() functions
     * @param string $post
     */
    protected function getCorrectedPostedDate($post)
    {
        if (strlen($post) == 10)  // when dates are dd-mm-yyyy, they are good
            return $post;
        
        // if j or n are in the date format, leading 0s are not required
        if (strpos($this->dateFormat, 'j') !== false || strpos($this->dateFormat, 'n') !== false)
            return $post;
        
        // if there is no delimiter in the date format, don't try to help users for now
        if ($this->dateFormatDelimiter == '')
            return $post;

        $elements = explode($this->dateFormatDelimiter, $post);
        array_walk($elements, function(&$var) {
            $var = str_pad($var, 2, '0', STR_PAD_LEFT); // str_pad will ignore strings longer than given 2
        });
        $post = implode($this->dateFormatDelimiter, $elements);

        return $post;
    }

    /**
     * render the date input element
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

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
        
        // TODO build date format translation based on $this->dateFormat
        
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
        if ($this->validate($options, 'not_empty|array'))
        {
            $this->jsOptions = $options;
        }
        
        return $this;
    }
    
    /**
     * set a date format
     * @param string $format
     */
    public function setDateFormat($format)
    {
        // I assume that when someone uses this method, they know how to write their format.
        // Almost all letter characters are allowed anyway: string(37) "dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU"
        $this->dateFormat = $format;
        
        // set the delimiter
        $this->setDateFormatDelimiter();
        
        // change the date format validation to the new format
        $this->replaceValidation('date_format:'.$this->dateFormat);
        
        // reset posted, (but not selected) because we correct posted dates that are missing leading zeroes
        if ($this->isPosted())
            $this->posted = $this->getCorrectedPostedDate($_POST[$this->name]);
        
        return $this;
    }
    
    /**
     * set date format delimiter
     * @return string
     */
    protected function setDateFormatDelimiter()
    {
        foreach (array('-', '/', '.', ' ') as $delimiter)
        {
            if (strpos($this->dateFormat, $delimiter) !== false)
            {
                $this->dateFormatDelimiter = $delimiter;
                return;
            }
        }
        
        $this->dateFormatDelimiter = '';
    }
    
}
