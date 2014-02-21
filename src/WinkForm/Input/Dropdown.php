<?php namespace WinkForm\Input;

class Dropdown extends Input
{

    protected $type = 'dropdown',
              $multiple = false,
              $optionsClasses = array(), // numeric array with classes for each option. indexes have to match index of values
              $optionsTitles = array();  // numeric array with titles for each option. indexes have to match index of values



    /**
     * render the dropdown
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        // create select tag
        $output = $this->renderLabel();

        // multi select dropdown?
        if ($this->multiple === true)
        {
            $output .= '<span class="dropdown-multipletext">Hold Ctrl to (de)select multiple.</span><br/>'."\n";
            $name = $this->renderName('[]');
        }
        else
        {
            $name = $this->renderName();
        }

        $output .= '<select'
                . $this->renderId()
                . $this->renderClass()
                . $name
                . $this->renderMultiple()
                . $this->renderSize()
                . $this->renderStyle()
                . $this->renderDisabled()
                . $this->renderTitle()
                . $this->renderDataAttributes()
                . $this->renderRequired()
                . $this->renderAutoFocus()
                .'>'."\n";

        // create options
        $lastCategory = '';
        foreach ($this->values as $i => $value)
        {
            // end category?
            if (! empty($this->categories[$i]) && ! empty($lastCategory) && $lastCategory != $this->categories[$i])
            {
                $output .= '</optgroup>'."\n";
            }
            // (new) category?
            if (! empty($this->categories[$i]) && $lastCategory != $this->categories[$i])
            {
                $output .= '<optgroup label="'.$this->categories[$i].'">'."\n";
                $lastCategory = $this->categories[$i];
            }

            // create option id
            $id = $this->id.'-'.$this->toValidHtmlId($value);

            // the actual option
            $output .= '<option id="'.$id.'" value="'.$value.'"'
                    . $this->renderOptionSelected($value)
                    . $this->renderOptionClass($i)
                    . $this->renderOptionTitle($i)
                    . '>'.$this->labels[$i]."</option>\n";

        }

        // end last category?
        if (! empty($lastCategory))
        {
            $output .= '</optgroup>'."\n";
        }

        $output .= '</select>'."\n";

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * @return string multiple="multiple"
     */
    public function renderMultiple()
    {
        return $this->multiple === true ? ' multiple="multiple"' : null;
    }

    /**
     * @param boolean $multiple
     * @return $this
     */
    public function setMultiple($multiple = true)
    {
        if ($this->validate($multiple, 'boolean'))
        {
            $this->multiple = $multiple;
        }

        return $this;
    }

    /**
     * set the classes for all options
     * @param array $optionsClasses
     * @return $this
     */
    public function setOptionsClasses($optionsClasses)
    {
        if ($this->validate($optionsClasses, 'array'))
        {
            $this->optionsClasses = $optionsClasses;
        }

        return $this;
    }

    /**
     * set the titles for all options (used by ChainedDropdowns jquery.chained.js)
     * @param array $optionsTitles
     * @return $this
     */
    public function setOptionsTitles($optionsTitles)
    {
        if ($this->validate($optionsTitles, 'array'))
        {
            $this->optionsTitles = $optionsTitles;
        }

        return $this;
    }

    /**
     * set class for one option
     * @param string $class
     * @return $this
     */
    public function appendOptionClass($class)
    {
        $this->optionsClasses[] = $class;

        return $this;
    }

    /**
     * set title for one option
     * @param string $title
     * @return $this
     */
    public function appendOptionTitle($title)
    {
        $this->optionsTitles[] = $title;

        return $this;
    }

    /**
     * get class of the option
     * @param int $i
     * @return string $class
     */
    protected function renderOptionClass($i)
    {
        return array_key_exists($i, $this->optionsClasses) ? ' class="'.$this->optionsClasses[$i].'"' : null;
    }

    /**
     * @param int $i
     * @return string $title
     */
    protected function renderOptionTitle($i)
    {
        return array_key_exists($i, $this->optionsTitles) ? ' title="'.$this->optionsTitles[$i].'"' : null;
    }

    /**
     * get the selected attribute if needed for the option
     * @param  string $value
     * @return string
     */
    protected function renderOptionSelected($value)
    {
        // selected has priority over value
        $selected = ! is_blank($this->selected) ? $this->selected : $this->value;

        if (is_blank($selected))
            return null;

        if (is_array($selected))
            return in_array($value, $selected) ? ' selected="selected"' : '';
        else
            return $value == $selected ? ' selected="selected"' : '';
    }

}
