<?php namespace WinkBrace\WinkForm\Input;

class Checkbox extends Input
{
    const ORIENTATION_HORIZONTAL = 0;
    const ORIENTATION_VERTICAL = 1;
    
    protected $type = 'checkbox';
    protected $renderInColumns;
    protected $orientation = self::ORIENTATION_HORIZONTAL;
    
    
    /**
     * render the hidden input element
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        $output = '';

        // I will assume style adjustments apply to the container div if there are more
        // checkboxes and that it will apply to the checkbox if there is only one.
                        
        // if it is a collection of checkboxes the property "values" is filled, otherwise the property "value"
        if (! empty($this->values))
        {
            $output .= $this->renderMultipleCheckboxes();
        }
        else // single checkbox
        {
            $output .= $this->renderSingleCheckbox();
        }
        
        // when posting, also send a hidden field so that if none of the options are selected
        // we still know that the checkbox has been posted
        require_once 'HiddenInput.php';
        $hidden = new HiddenInput($this->name.'-isPosted', 1);
        $output .= $hidden->render();
        
        $output .= $this->renderInvalidations();
        
        // return or echo output
        return $output;
    }
    
    /**
     * render mutliple checkboxes
     * @return string $output
     */
    protected function renderMultipleCheckboxes()
    {
        $output = '';
        
        // get the selected values
        $selectedValues = array(); // default empty array
        if (isset($this->selected))
        {
            if (is_array($this->selected))
                $selectedValues = $this->selected;
            else
                $selectedValues = array($this->selected);
        }
        
        if (! empty($this->renderInColumns))
        {
            $rowsPerColumn = ceil(count($this->values) / $this->renderInColumns);
        }
        
        $columns = array(); // array to collect the values per column in
        $classesAtStart = $this->classes;
        foreach ($this->values as $i => $value)
        {
            $checked = in_array($value, $selectedValues) ? ' checked="checked"' : '';
            $id = $this->id.'-'.$this->toValidHtmlId($value);
        
            // add category class if it exists
            if (isset($this->categories[$i]))
            {
                $this->addClass(str_replace(' ', '', $this->categories[$i]));
            }
        
            $checkbox = '<input'
                . $this->renderType()
                . ' id="'.$id.'"'
                . $this->renderClass()
                . $this->renderName('[]')
                . ' value="'.$value.'"'
                . $checked
                . $this->renderStyle()
                . $this->renderDisabled()
                . $this->renderTitle()
                . $this->renderDataAttributes()
                . ' />'."\n";
        
            if (isset($this->labels[$i])) // I also want to display 0
            {
                $checkbox .= '<label for="'.$id.'">'.$this->labels[$i].'</label>'."\n";
            }
        
            // render in columns per category, per given columns number, or don't render in columns
            if (! empty($this->categories[$i]))
            {
                $columns[$this->categories[$i]][] = $checkbox;
            }
            elseif (! empty($this->renderInColumns))
            {
                $columns[floor($i / $rowsPerColumn)][] = $checkbox;
            }
            else
            {
                $output .= $checkbox;
            }
        
            // reset classes, so the last entry doesn't have all category classes :)
            $this->classes = $classesAtStart;
        }
        
        if (! empty($columns))
        {
            // recreate the $output
            $output = '<table><tr>';
            $colcount = 0;
            foreach ($columns as $key => $col)
            {
                $output .= '<td valign="top">';
                if (! empty($this->categories))
                {
                    $categoryId = str_replace(' ', '', $key);
                    $output .= '<input type="checkbox" class="'.$this->name.'-category" name="'.$categoryId.'" id="'.$categoryId.'">
                                    <label for="'.$categoryId.'" style="font-weight:bold;">'.$key."</label></strong><br/>\n";
                }
        
                foreach ($col as $checkbox)
                {
                    $output .= $checkbox."<br/>\n";
                }
        
                $output .= '</td>';
        
                // go to new table row if the number of columns set in renderInColumns is reached. Add empty td for spacing.
                if (! empty($this->categories) && ! empty($this->renderInColumns) && ++$colcount % $this->renderInColumns == 0)
                    $output .= '</tr><tr><td colspan="'.$this->renderInColumns.'" style="padding-top:10px;"></td></tr><tr>'."\n";
            }
        
            $output .= '</tr></table>'."\n";
        
            // add js for the category checkboxes
            if (! empty($this->categories))
            {
                $output .= '<script type="text/javascript">
                                // category checkbox: check all underlying checkboxes when a category name is clicked
                                $("input.'.$this->name.'-category").click(function() {
                                    var classname = $(this).attr("name");
                                    var bool = $(this).is(":checked");
                                    $("."+classname).attr("checked", bool);
                                });
                                </script>'."\n";
            }
        }
        
        $output = $this->renderLabel().'<div id="'.$this->id.'-container"'.$this->renderStyle().'>'."\n".$output."</div>\n";
        
        return $output;
    }
    
    /**
     * render single checkbox
     * @return string $output
     */
    protected function renderSingleCheckbox()
    {
        if (empty($this->value))
        {
            $this->setValue('off');
            if ($this->selected === true) // for intuitive use
                $this->selected = 'off';
        }
        
        $checked = $this->value == $this->selected ? ' checked="checked" ' : '';
        
        $checkbox = '<input'
            . $this->renderType()
            . $this->renderId()
            . $this->renderClass()
            . $this->renderName()
            . $this->renderValue()
            . $checked
            . $this->renderStyle()
            . $this->renderDisabled()
            . $this->renderTitle()
            . $this->renderDataAttributes()
            . $this->renderRequired()
            . ' />'."\n";
        
        if (! empty($this->label))
        {
            $checkbox .= '<label for="'.$this->id.'">'.$this->label.'</label>'."\n";
        }
        
        return '<div id="'.$this->id.'-container"'.$this->renderStyle().'>'."\n".$checkbox."</div>\n";
    }
    
    /**
     * @param string $selected
     */
    public function setSelected($selected, $flag = 0)
    {
        // check the hidden input field that is always send
        if (! isset($_POST[$this->name.'-isPosted']) || $this->isFlagSet($flag, self::INPUT_OVERRULE_POST))
        {
            // checkboxes can be array or single value. If array of values is given, make sure selected is an array
            if (! empty($this->values) && ! is_array($selected) && strlen($selected) != 0)
            {
                // assume commas mean a comma separated list
                if (strpos($selected, ',') !== false)
                    $selected = explode(',', $selected);
                else
                    $selected = array($selected);
            }
                
            $this->selected = $selected;
        }
        
        return $this;
    }
    
    /**
     * store posted values
     * @param $posted
     */
    protected function setPosted()
    {
        if (isset($_POST[$this->name.'-isPosted']) && isPosted($this->name)) // check the hidden input field that is always send
        {
            // checkboxes can be array or single value. If array of values is given, make sure posted is an array
            $posted = $_POST[$this->name];
            if (! empty($this->values) && ! is_array($posted))
                $posted = array($posted);
            
            $this->posted = $posted;
            $this->selected = $posted;  // so we can always retrieve the selected fields with getSelected()
        }
        
        return $this;
    }
    
    /**
     * is this input element posted?
     * @return boolean
     */
    public function isPosted()
    {
        return (isset($_POST[$this->name.'-isPosted']) && isPosted($this->name));
    }
    
    /**
     * give amount of columns the list should be displayed in
     * @param int $int
     */
    public function setRenderInColumns($int)
    {
        if ($this->validator->numeric($int))
        {
            $this->renderInColumns = $int;
        }
        
        return $this;
    }
    
    /**
     * set the orientation (place the fields horizontally or vertically on screen)
     * @param string $orientation
     */
    public function setOrientation($orientation)
    {
        if ($this->validator->inArray($orientation, array(static::ORIENTATION_HORIZONTAL, static::ORIENTATION_VERTICAL)))
        {
            $this->orientation = $orientation;
            $this->setRenderInColumns($orientation);
        }
        
        return $this;
    }
    
    /**
     * @return renderInColumns
     */
    public function getRenderInColumns()
    {
        return $this->renderInColumns;
    }
    
    /**
     * @return orientation
     */
    public function getOrientation()
    {
        return $this->orientation;
    }
    
}
