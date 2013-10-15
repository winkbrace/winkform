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
              $dateFormatDelimiter,
              $calendarImage;

    /**
     * @var TextInput
     */
    protected $text;


    /**
     * construct DateInput
     * @param string $name
     * @param mixed $value
     */
    function __construct($name, $value = null)
    {
        // set date format
        $config = require WINKFORM_PATH.'config.php';
        $this->setDateFormat($config['date_format']);

        parent::__construct($name, $value);

        // create TextInput object with all same properties as this DateInput object
        $this->text = new TextInput($this->name, $value);
        $this->text->setWidth(80)->setMaxLength(10);

        $this->attachObserver($this->text);

        // set default calendar image (base64 encoded, so no actual image file required to use this class)
        // It is of course better to use an actual image for performance reasons
        $this->calendarImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAACpklEQVRYheWXPWgU'
            . 'QRTHf5dLxIgiBEQtEgRR4gdiLShWojZ+gbYWthaijSCWgoWVBK1URJAjhYKKX6DEFEpADeYM+IGiSBJzaESNXvwai/cm+9yb3dsNF1L'
            . '4YGF+M/tm/vPmzcxuwTnHTFrTjI4O4JyjThS2AQeBlpxdb1e/Wanj1hHQAlQAB6zLMXiz8duUJqDZ1K0F2mPvfQZ+a7kTaAMKGQRUjd'
            . '8qJArWbwh4+I8S4KIqtk8JeKnlrkB70nMTeKHl84H2y35cm4QTsVl8AU4Bs5VvAb0ZZv8VOA60Kt8GemLvTI6VtgvKQB8wT3kUuJpBQ'
            . 'Bl4YPwqwLWkl9MEdCDreANZhjKSJ/WsPebXl+pncuActWvVDawGlgOHteMsOXDJ+B0K+JVC2zAkYLqeUmgbeqsAp/VFkGUqEG2rrBby'
            . '24ss7aSFBAwDR3MOltU2xAWEkrCYUN8Iq5lwKAJJtgjYqeV+4DWwQ/k+MAZsVe4BvgOble8hu6jWAklYJhyB9URJdBLYaPgIsMvwfmC'
            . 'P4QPaRy+xJJxqqH3HWTnR8izBIDJLkHN+yPAAcgR7fowct5778wpwwFLgmPJ15ILZolxE1tjzODBi+ANyl3geA17lFbAA2K38Cbk+9y'
            . 'lXkbvB8xskQp6fxNoHgbt5BAD8IjpEvmnZsm2vAj8NTyg7w0FLEtAEPAXWKH9EwujZh9jze+CH4WEV0Kk8mlfAHyQHSsoXkIupW/ksc'
            . 'EfrHXACORe6DJ8BnicNXE8AwFxgpZaXIB8YnjuQ+36F8mIkTyxnsjQB48AzLb9Fst7zO2TbeR5RAZanLMAnzgDRGnrLy0l9pwooAvOR'
            . 'PPAO/os2D8e/nl1ovJCAZUShbLS1pQloNXULp0mAtzkhAVdI2a8Ntke+UPjv/47/Ajh1PTcN0TPzAAAAAElFTkSuQmCC';
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
     * Override setValue to always validate date format
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        if (empty($value) || $this->validate($value, 'not_array|date_format:'.$this->dateFormat))
        {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * This is a fix for when users manually input dates without using leading 0s
     * We can think of a lot more checks, but let's keep it as fast as possible. Real validation is in the validate() functions
     * @param string $post
     * @return string
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
     * @return string
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        // we will show/hide the container div for the text field and the image and not the text field and the image themselves
        $hidden = $this->getHidden() === true ? ' style="display:none;"' : '';
        $this->setHidden(false);

        $output = '<div id="'.$this->id.'-container"'.$hidden.' style="float: left;">'
                . $this->text->render()
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

        // There will be no validation here, it's assumed the user has knowledge
        // of the possible arguments. Only some defaults will be provided

        // Merge in defaults.
        $options += array(
            'dateFormat'      => $this->getJSDateFormat(),
            'firstDay'        => 1,
            'showButtonPanel' => true,
            'showWeek'        => true,
            'changeMonth'     => true,
            'changeYear'      => true,
            'showOn'          => 'both',
            'buttonImage'     => $this->calendarImage,
            'buttonImageOnly' => true,
            'buttonText'      => 'Pick a date',
        );

        $json = json_encode($options);

        return $json;
    }

    /**
     * translate the php date format to jquery date picker date format
     * @return string
     * @throws \Exception
     */
    protected function getJSDateFormat()
    {
        if (empty($this->dateFormat))
            return null;

        // array with php => jquery date picker date formats
        $translation = array(
            'j' => 'd',     // day of the month
            'd' => 'dd',    // day of the month with leading 0
            'z' => 'o',     // day of the year
            'D' => 'D',     // day of the week short: 'Mon'
            'l' => 'DD',    // day of the week full: 'Monday'
            'n' => 'm',     // month of the year
            'm' => 'mm',    // month of the year with leading 0
            'M' => 'M',     // month name short
            'F' => 'MM',    // month name full
            'y' => 'y',     // year 2 digit
            'Y' => 'yy',    // year 4 digit
        );

        $jsDateElements = array();
        $dateElements = explode($this->dateFormatDelimiter, $this->dateFormat);
        foreach ($dateElements as $el)
        {
            if (! array_key_exists($el, $translation))
                throw new \Exception('Untranslatable date format specified for date picker');

            $jsDateElements[] = $translation[$el];
        }

        return implode($this->dateFormatDelimiter, $jsDateElements);
    }

    /**
     * Set extra parameters or overwrite default ones for the DatePicker.
     * @param array $options
     * @return $this
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
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setDateFormat($format)
    {
        // only accept date formats that can be translated to jquery date picker
        $allowed = array('-', '/', '.', ' ', 'j', 'd', 'z', 'D', 'l', 'n', 'm', 'M', 'F', 'y', 'Y');
        for ($i = 0; $i < strlen($format); $i++)
        {
            if (! in_array($format[$i], $allowed))
                throw new \InvalidArgumentException('Invalid date format given. Only allowed characters are: "'.implode('", "', $allowed).'"');
        }

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

    /**
     * set calendar image url
     * @param string $calendarImage
     */
    public function setCalendarImage($calendarImage)
    {
        $this->calendarImage = $calendarImage;
    }

}
