<?php namespace WinkForm\Input;

/**
 * Abstract class for input classes
 * Only render() _has_ to be different for each concrete class. The rest can be inherited.
 * @author b-deruiter
 *
 */
abstract class Input
{
    protected $type,
              $name,
              $label,
              $id,
              $value,
              $values = array(),
              $labels = array(),
              $categories,
              $width,
              $classes = array(),
              $title,
              $styles = array(),
              $selected,
              $posted,
              $disabled,
              $hidden,
              $onclick,
              $onchange,
              $size, // File Input doesn't support style="width" and Dropdown also uses it
              $inReportForm = false,
              $required = false,         // boolean value to tell if the input element is required
              $validations = array(),    // array of validations. @see http://laravel.com/docs/validation#available-validation-rules
              $invalidations = array(),  // result of form validations of this input element
              $dataAttributes = array(), // custom data-attributes can be collected in this array
              $autoFocus = false,        // boolean value if the input element should get focus when the page is loaded
              $placeholder;              // specifies a short hint that describes the expected value of an input field

    protected $validator; // Validate object (this validates the object attributes are properly set, not the form validation!)
    
    // bitwise flags
    const INPUT_OVERRULE_POST = 1; // make the given selected value overwrite anything that is posted
    const INPUT_SELECTED_INITIALLY_ONLY = 2; // only use an initial "selected" value if nothing has been posted yet
    const INPUT_DONT_ESCAPE_HTML = 4; // used by setLabels() and appendOptions to not escape HTML chars
    // next const should be 8, then 16 etc. to have the nth bit set to 1

    
    /**
     * construct Input
     * @param string $name
     * @param mixed $value
     */
    function __construct($name, $value = null)
    {
        $this->validator = new \WinkForm\Validation\Validator();
        
        $this->setName($name);
        $this->setId($name); // normally you want the id to be the same as the name
        
        if (! empty($value))
        {
            if (is_array($value))
                $this->setValues($value);
            else
                $this->setValue($value);
        }
        
        // store posted as selected
        $this->setPosted();
    }
    
    /**
     * render html
     * @return string
     */
    abstract public function render();

    /**
     * This should always be called at the start of render()
     * check result of validity checks of parameters passed to this Input element
     * @throws \Exception
     */
    protected function checkValidity()
    {
        if (! $this->validator->passes())
            throw new \Exception($this->renderValidationErrors('Error rendering '.get_class($this).' object with name '.$this->name));
    }

    /**
     * convert all characters in $value that are not allowed in a html string to $replace (default a dash -)
     * @param string $value
     * @param string $replace
     * @return string
     */
    protected function toValidHtmlId($value, $replace = '-')
    {
        $invalidCharacters = str_split(" \\\r\n\t;,./&|[]{}+=`~!@#$%^*()'\"");
        return str_replace($invalidCharacters, $replace, $value);
    }
    
    /**
     * bitwise check if given flag number contains the wanted value
     *
     * @param int $flag
     * @param int $value
     * @return boolean
     */
    protected function isFlagSet($flag, $value)
    {
        if (empty($flag) || empty($value))
            return false;
        
        return (($flag & $value) == $value);
    }
    
    
    /**
     * @return string id="$id"
     */
    public function renderId()
    {
        return ' id="'.$this->id.'"';
    }
    
    /**
     * return only the contents of the id attribute
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return string type="$type"
     */
    public function renderType()
    {
        return ' type="'.$this->type.'"';
    }

    /**
     * @param  string $array '[]' or null
     * @return string $name
     */
    public function renderName($array = null)
    {
        $name = $array == '[]' && strpos($this->name, '[]') === false ? $this->name.'[]' : $this->name;
        return ' name="'.$name.'"';
    }
    
    /**
     * return only the contents of the name attribute
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string $value
     */
    public function renderValue()
    {
        $value = ! empty($this->selected) ? $this->selected : $this->value;
        return ! empty($value) ? ' value="'.$value.'"' : null;
    }
    
    /**
     * @return $value
     */
    public function getValue()
    {
        return empty($this->selected) ? $this->value : $this->selected;
    }

    /**
     * @return array $values
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return string $label
     */
    public function renderLabel()
    {
        if (empty($this->label) || $this->inReportForm)
            return null;
            
           $class = $this->required ? ' class="required"' : '';
           return '<label for="'.$this->id.'"'.$class.'>'.$this->label.'</label> ';
    }
    
    /**
     * @return string placeholder="$placeholder"
     */
    public function renderPlaceholder()
    {
        return ! empty($this->placeholder) ? ' placeholder="'.$this->placeholder.'"' : null;
    }
    
    /**
     * return only the label attribute
     * @return string $label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array $labels
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @return int $width
     */
    public function getWidth()
    {
        return $this->width;
    }
    
    /**
     * @return string $class
     */
    public function renderClass()
    {
        return ! empty($this->classes) ? ' class="'.implode(' ', $this->classes).'"' : null;
    }
    
    /**
     * @return array $classes
     */
    public function getClasses()
    {
        return $this->classes;
    }
    
    /**
     * Prepares the inline style for an input element
     *
     * @return NULL|string final inline style
     */
    public function renderStyle()
    {
        // use getStyles() to make sure all the styles are fetched in a uniform way
        $styles = $this->getStyles();

        if (empty($styles))
            return null;
        
        $inlineStyle = array();
        foreach ($styles as $attribute => $value)
            $inlineStyle[] = $attribute . ':' . $value;
        
        return ' style="' . implode('; ', $inlineStyle) . ';"';
    }
    
    /**
     *
     * @return array $styles
     */
    public function getStyles()
    {
        /* This gives child input elements that are part of an another input
         * (e.g. WeekRange) the possibility to still have their own width and
         * hidden visibility by overwriting the copied styles, just before render.
         */
        if ($this->width)
            $this->addStyle(array('width' => $this->width . 'px'));
        
        if ($this->hidden)
            $this->addStyle(array('display' => 'none'));
            
        return $this->styles;
    }
    
    /**
     * @return mixed $selected
     */
    public function getSelected()
    {
        return $this->selected;
    }
    
    /**
     * @return string $posted
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * @return string $disabled
     */
    public function renderDisabled()
    {
        return $this->isDisabled() ? ' ' . $this->disabled . '="' . $this->disabled . '"' : null;
    }
    
    /**
     * @return boolean
     */
    public function isDisabled()
    {
        return ! empty($this->disabled);
    }
    
    /**
     * @return boolean $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }
    
    /**
     * @return boolean $hidden
     */
    public function isHidden()
    {
        return $this->hidden;
    }
    
    /**
     * @return boolean $inReportForm
     */
    public function getInReportForm()
    {
        return $this->inReportForm;
    }
    
    /**
     * @return string $title
     */
    public function renderTitle()
    {
        return ! empty($this->title) ? ' title="'.$this->title.'"' : null;
    }
    
    /**
     * @return string $size
     */
    public function renderSize()
    {
        return ! empty($this->size) ? ' size="'.$this->size.'"' : null;
    }
    
    /**
     * @return string $required
     */
    public function renderRequired()
    {
        return $this->required ? ' required' : null;
    }
    
    /**
     * @return string autofocus attribute if it was set
     */
    public function renderAutoFocus()
    {
        return $this->autoFocus ? ' autofocus' : null;
    }
    
    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        // a name can contain [] (or [bla][][][]) but the id not
        $id = str_replace(array('[',']'), '_', $id);
            
        if ($this->validate($id, 'alpha_dash'))
        {
            $this->id = $id;
        }
        
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function setName($name)
    {
        // a name can contain [ and ], but nothing else
        $testName = str_replace(array('[',']'), '_', $name);
        if ($this->validate($testName, 'alpha_dash'))
        {
            $this->name = $name;
        }
        
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        if ($this->validate($value, 'not_array'))
        {
            $this->value = xsschars($value);
        }
        
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setValues($values)
    {
        if ($this->validate($values, 'array'))
        {
            $values = array_values($values); // enforce numeric array
            array_walk($values, 'xsschars');
            
            $this->values = $values;
        }
        
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        
        return $this;
    }
    
    /**
     * @param array $labels
     * @param optional int $flag
     * @return $this
     */
    public function setLabels($labels, $flag = null)
    {
        if ($this->validate($labels, 'array'))
        {
            $labels = array_values($labels); // enforce numeric array
            
            if (! $this->isFlagSet(self::INPUT_DONT_ESCAPE_HTML, $flag))
                array_walk($labels, 'xsschars');
            
            $this->labels = $labels;
        }
        
        return $this;
    }
    
    /**
     * Sets the value for the placeholder attribute
     * The placeholder attribute works with the following input types:
     * text, search, url, tel, email, and password.
     *
     * @param string $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        if ($this->validate($this->type, 'in:text,search,url,tel,email,password'))
        {
            $this->placeholder = xsschars($placeholder);
        }

        return $this;
    }
    
    /**
     * Sets the autofocus attribute for the input field. The input will
     * get focus when the page loads
     *
     * @param boolean $flag
     * @return $this
     */
    public function setAutoFocus($flag)
    {
        if ($this->validate($flag, 'boolean'))
        {
            $this->autoFocus = $flag;
        }

        return $this;
    }
    
    /**
     * Append individual option <option value="$value">$option</option>
     * @param string $value
     * @param string $label
     * @param optional string $category
     * @param optional int $flag
     * @return $this
     */
    public function appendOption($value, $label, $category = null, $flag = null)
    {
        if ($this->isFlagSet(self::INPUT_DONT_ESCAPE_HTML, $flag))
        {
            $this->values[] = $value;
            $this->labels[] = $label;
        }
        else
        {
            $this->values[] = xsschars($value);
            $this->labels[] = xsschars($label);
        }
        
        if (! empty($category))
            $this->categories[] = $category;
        
        return $this;
    }
    
    /**
     * append array of options ( $value => $label )
     * @param $options
     * @param optional int $flag
     * @param string $category    - You can optionally specify one category for all options in the array
     * @return $this
     */
    public function appendOptions($options, $flag = null, $category = null)
    {
        if ($this->validate($options, 'array'))
        {
            // loop over the options array and add all values to values and labels
            // array_merge or the + operator won't do, because they will remove duplicate keys
            // (and we will have a lot of duplicate keys in 2 numeric arrays)
            foreach ($options as $value => $label)
                $this->appendOption($value, $label, $category, $flag);
        }
        
        return $this;
    }
    
    /**
     * Prepend individual option <option value="$value">$option</option>
     * @param string $value
     * @param string $label
     * @param optional string $category
     * @param optional int $flag
     * @return $this
     */
    public function prependOption($value, $label, $category = null, $flag = null)
    {
        // prepend values to array (Yes, this will set all other numeric keys 1 higher)
        if ($this->isFlagSet(self::INPUT_DONT_ESCAPE_HTML, $flag))
        {
            array_unshift($this->values, $value);
            array_unshift($this->labels, $label);
        }
        else
        {
            array_unshift($this->values, xsschars($value));
            array_unshift($this->labels, xsschars($label));
        }
        
        if (! empty($category))
            array_unshift($this->categories, $category);
        
        return $this;
    }
    
    /**
     * prepend array of options ( $value => $label )
     * @param $options
     * @param optional int $flag
     * @param string $category    - You can optionally specify one category for all options in the array
     * @return $this
     */
    public function prependOptions($options, $flag = null, $category = null)
    {
        if ($this->validate($options, 'array'))
        {
            // loop over the options array and add all values to values and labels
            // array_merge or the + operator won't do, because they will remove duplicate keys
            // (and we will have a lot of duplicate keys in 2 numeric arrays)
            $options = array_reverse($options); // reverse the array to keep the order when we unshift :)
            foreach ($options as $value => $label)
            {
                $this->prependOption($value, $label, $category, $flag);
            }
        }
        
        return $this;
    }
    
    /**
     * remove an option (= value and label) by providing the value of that option
     * @param string $value
     * @return $this
     */
    public function removeOption($value)
    {
        $i = array_search($value, $this->values);
        if ($i !== false)
        {
            unset($this->values[$i]);
            unset($this->labels[$i]);
        }

        return $this;
    }
    
    /**
     * set categories for dropdowns and checkboxes
     * @param array $categories
     * @return $this
     */
    public function setCategories($categories)
    {
        if ($this->validate($categories, 'array'))
        {
            $this->categories = array_values($categories); // enforce numeric array
        }
    
        return $this;
    }

    /**
     * @param int $width (in pixels)
     * @return $this
     */
    public function setWidth($width)
    {
        if ($this->validate($width, 'numeric'))
        {
            $this->width = $width;
        }

        return $this;
    }

    /**
     * @param array $classes
     * @return $this
     */
    public function setClass($classes)
    {
        if (! is_array($classes))
            $classes = explode(' ', trim($classes));
        
        foreach ($classes as $class)
        {
            if ($this->validate($class, 'alpha_dash'))
            {
                $this->classes[] = $class;
            }
        }
        
        return $this;
    }

    /**
     * add a class or a list of classes separated by a space, just like in html
     * @param string $class
     * @return $this
     */
    public function addClass($class)
    {
        $classes = explode(' ', $class);
        foreach ($classes as $cls)
        {
            if ($this->validate($cls, 'alpha_dash'))
            {
                if (! in_array($cls, $this->classes))
                    $this->classes[] = $cls;
            }
        }
        
        return $this;
    }
    
    /**
     * remove a class from the classes
     * @param string $class
     * @return $this
     */
    public function removeClass($class)
    {
        foreach ($this->classes as $key => $val)
        {
            if ($val == $class)
                unset($this->classes[$key]);
        }
        
        return $this;
    }
    
    /**
     * Resets the inline style and adds the new one
     *
     * @param string|array $styles
     * @return $this
     */
    public function setStyle($styles)
    {
        // use addStyle to keep the logic in one function
        $this->styles = array();
        $this->addStyle($styles);
        
        return $this;
    }
    
    /**
     * Adds additional attributes to the inline style of the input element
     *
     * @param string|array $style  either a string (e.g. 'color:red; padding: 8px'),
     *                             or an array (e.g. array('color' => 'red', 'padding' => '8px'));
     * @return $this
     */
    public function addStyle($style)
    {
        $styles = (is_array($style)) ? $style : $this->parseStyleToArray($style);
        
        foreach ($styles as $attribute => $value)
        {
            $this->styles[$attribute] = trim($value);
        }
        
        return $this;
    }
    
    /**
     * Removes attributes from the inline style of the input element
     *
     * @param string|array $style   either a string (e.g. 'color:red; padding: 8px'),
     *                              or an array (e.g. array('color' => 'red', 'padding' => '8px'));
     * @return $this
     */
    public function removeStyle($style)
    {
        $styles = (is_array($style)) ? $style : $this->parseStyleToArray($style);
        
        foreach ($styles as $attribute => $value)
            unset($this->styles[$attribute]);
        
        return $this;
    }
    
    /**
     * Parses a string an returns an array where the key is the CSS attribute
     * and the value is the CSS atribute's value
     *
     * @param string $style
     * @return array
     */
    public function parseStyleToArray($style)
    {
        if (empty($style))
            return array();
    
        $params = array();
    
        $elements = explode(';', $style);
        $elements = array_map('trim', $elements);
        $elements = array_filter($elements);
    
        foreach($elements as $element)
        {
            list($key, $value) = explode(':', $element);
            $params[trim($key)] = trim($value);
        }
    
        return $params;
    }
    
    /**
     * Set an initial value for the input field
     * @param string $selected
     * @param int $flag
     * @return $this
     */
    public function setSelected($selected, $flag = 0)
    {
        if (empty($this->posted) || $this->isFlagSet($flag, self::INPUT_OVERRULE_POST))
        {
            // of flag is niet geset of wel geset maar dan moet POST empty zijn
            if (! $this->isFlagSet($flag, self::INPUT_SELECTED_INITIALLY_ONLY) || empty($_POST))
            {
                $this->selected = $selected;
            }
        }
        
        return $this;
    }
    
    /**
     * store posted values
     */
    protected function setPosted()
    {
        if (! empty($_POST[$this->name]))
        {
            $post = $_POST[$this->name];
            if (is_array($post))
                array_walk($post, 'xsschars');
            else
                $post = xsschars($post);

            $this->posted = $post;
            $this->selected = $post;  // so we can always retrieve the selected fields with getSelected()
        }
    }
    
    /**
     * is this input element posted?
     * @return boolean
     */
    public function isPosted()
    {
        return ! empty($_POST[$this->name]);
    }
    
    /**
     * set $disabled
     * @param string $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        if ($this->validate($disabled, 'in:disabled,readonly'))
        {
            $this->disabled = $disabled;
        }
        
        return $this;
    }
    
    /**
     * @param boolean $hidden
     * @return $this
     */
    public function setHidden($hidden)
    {
        if ($this->validate($hidden, 'boolean'))
        {
            $this->hidden = $hidden;
            
            if ($this->hidden === false)
                $this->removeStyle(array('display'));
        }
        
        return $this;
    }
    
    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        
        return $this;
    }
    
    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        if ($this->validate($size, 'numeric'))
        {
            $this->size = $size;
        }
        
        return $this;
    }
    
    /**
     * indicator to let Input know if it's in the ReportForm class or not (needed to display labels or not)
     * @param bool $bool
     * @return $this
     */
    public function setInReportForm($bool)
    {
        if ($this->validate($bool, 'boolean'))
        {
            $this->inReportForm = $bool;
        }
        
        return $this;
    }
    
    /**
     * @return boolean $required
     */
    public function isRequired()
    {
        return $this->required === true;
    }

    /**
     * @param boolean $required
     * @return $this
     */
    public function setRequired($required = true)
    {
        if ($this->validate($required, 'boolean'))
        {
            $this->required = $required;
            
            if ($required)
                $this->addClass('required');
            else
                $this->removeClass('required');
        }
        
        return $this;
    }
    
    /**
     * @return array $invalidations
     */
    public function getInvalidations()
    {
        return $this->invalidations;
    }

    /**
     * @param string $invalidation
     * @return $this
     */
    public function addInvalidation($invalidation)
    {
        if (! in_array($invalidation, $this->invalidations))
        {
            $this->invalidations[] = $invalidation;
            $this->addClass('invalid');
        }
        
        return $this;
    }
    
    /**
     * render the custom data attributes
     * @return string $output
     */
    public function renderDataAttributes()
    {
        if (empty($this->dataAttributes))
            return null;
        
        $output = '';
        foreach ($this->dataAttributes as $name => $value)
        {
            $name =  ! str_like($name, 'data-%') ? 'data-'.$name : $name;
            $output .= ' '.$name.'="'.$value.'"';
        }
        
        return $output;
    }
    
    /**
     * set all custom data attributes
     * Note: this will overwrite any previously set or added data attributes
     * @param array $dataAttributes
     * @return $this
     */
    public function setDataAttributes($dataAttributes)
    {
        if ($this->validate($dataAttributes, 'array') && $this->validate($dataAttributes, 'numeric_array'))
        {
            $this->dataAttributes = $dataAttributes;
        }
        
        return $this;
    }
    
    /**
     * add a custom data attribute (Example: <input ... data-answer_to_life="42">)
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addDataAttribute($name, $value)
    {
        $this->dataAttributes[$name] = $value;
        
        return $this;
    }
    
    /**
     * remove a custom data attribute
     * @param string $name
     * @return $this
     */
    public function removeDataAttribute($name)
    {
        if (array_key_exists($name, $this->dataAttributes))
            unset($this->dataAttributes[$name]);
        
        return $this;
    }
    
    /**
     * Add validation for input field. This validation must be executed by a form->validate() or in a script after posting.
     * @see http://laravel.com/docs/validation#available-validation-rules
     * The rules must exist in the \WinkForm\Validation\WinkValidator class
     * @param string|array $rules
     * @return $this
     */
    public function addValidation($rules)
    {
        $rules = (is_string($rules)) ? explode('|', $rules) : $rules;
        foreach ($rules as $rule)
        {
            if (! in_array($rule, $this->validations))
                $this->validations[] = $rule;
        }
        
        return $this;
    }
    
    /**
     * remove validation for Input element
     * @param string|array $rules
     */
    public function removeValidation($rules)
    {
        $rules = (is_string($rules)) ? explode('|', $rules) : $rules;
        
        foreach ($rules as $rule)
        {
            // cut off everthing from the colon
            $rule = $this->getRawRuleName($rule);
            
            foreach ($this->validations as $i => $validation)
            {
                if ($this->getRawRuleName($validation) == $rule)
                    unset($this->validations[$i]);
            }
        }
        
        // reset validations keys to be incrementing again
        $this->validations = array_values($this->validations);
    }
    
    /**
     * replace validation for Input element
     * @param string|array $rules
     */
    public function replaceValidation($rules)
    {
        $this->removeValidation($rules);
        $this->addValidation($rules);
    }
    
    /**
     * get the raw rule name, omitting everything from the colon
     * @param string $rule
     * @return string
     */
    protected function getRawRuleName($rule)
    {
        return strpos($rule, ':') !== false ? substr($rule, 0, strpos($rule, ':')) : $rule;
    }
    
    /**
     * get array of validations. Each as array('validation' => '', 'parameters' => array())
     * @return array $validations
     */
    public function getValidations()
    {
        return $this->validations;
    }
    
    /**
     * Does this Input element have validations?
     * @return boolean
     */
    public function hasValidations()
    {
        return count($this->validations) > 0;
    }
    
    /**
     * is the input valid?
     * @return boolean
     */
    public function isValid()
    {
        return empty($this->invalidations);
    }

    /**
     * render the invalidations
     * @return string
     */
    protected function renderInvalidations()
    {
        if (empty($this->invalidations))
            return null;
        
        return '<div class="invalidations">'.implode("<br/>\n", $this->invalidations)."</div>\n";
    }

    /**
     * calls $this->validator and provides Input name
     * This method must only be used to validate the parameters of the Input object and NOT the posted values.
     * @param string $value        the value to test
     * @param string|array $rules  the rules to test against
     * @param string $message      custom error message
     */
    protected function validate($value, $rules, $message = null)
    {
        return $this->validator->validate($this->name, $value, $rules, $message);
    }
    
    /**
     * return the found error messages using $this->validate()
     * optionally prefixed with given message and optionally formatted in error div
     * @param null $message
     * @param bool $inErrorDiv
     * @return string
     */
    public function renderValidationErrors($message = null, $inErrorDiv = true)
    {
        $errors = $this->validator->getAttributeErrors($this->name);

        if (empty($errors))
            return null;

        $errorString = implode("<br/>\n", $errors);

        $message = ! empty($message) ? '<p>'.$message."</p>\n" : "";

        if ($inErrorDiv === true)
            $message = '<div class="error">' . $message . "<p>" . $errorString . "</p></div>\n";
        else
            $message = $message.$errorString;

        return $message;
    }

    /**
     * when an Input object is echo'd, return the render()
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
    
}
