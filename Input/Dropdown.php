<?php namespace WinkBrace\WinkForm\Input;

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
            
            // id
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
     */
    public function setMultiple($multiple = true)
    {
        if ($this->validator->validate($multiple, 'boolean'))
        {
            $this->multiple = $multiple;
        }
        
        return $this;
    }
    
    /**
     * set the classes for all options
     * @param array $optionsClasses
     */
    public function setOptionsClasses($optionsClasses)
    {
        if ($this->validator->validate($optionsClasses, 'array'))
        {
            $this->optionsClasses = $optionsClasses;
        }
        
        return $this;
    }
    
    /**
     * set the titles for all options (used by ChainedDropdowns jquery.chained.js)
     * @param array $optionsTitles
     */
    public function setOptionsTitles($optionsTitles)
    {
        if ($this->validator->validate($optionsTitles, 'array'))
        {
            $this->optionsTitles = $optionsTitles;
        }
    
        return $this;
    }
    
    /**
     * set class for one option
     * @param string $class
     */
    public function appendOptionClass($class)
    {
        $this->optionsClasses[] = $class;
        
        return $this;
    }
    
    /**
     * set title for one option
     * @param string $title
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
     * @param string $value
     */
    protected function renderOptionSelected($value)
    {
        foreach (array($this->selected, $this->value) as $selected)
        {
            if (! empty($selected))
            {
                if (is_array($selected))
                    return in_array($value, $selected) ? ' selected="selected"' : '';
                else
                    return $value == $selected ? ' selected="selected"' : '';
            }
        }
        
        return null;
    }
    
}
