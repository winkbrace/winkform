<?php namespace WinkBrace\WinkForm\Input;

class Radio extends Input
{
    
    protected $type = 'radio',
              $renderInColumns,
              $lineEnd;
    
    
    /**
     * render the radio input element
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        $output = '';
        
        // if it is a collection of checkboxes the property "values" is filled, else the property "value"
        if (! empty($this->values))
        {
            if (! empty($this->renderInColumns))
            {
                $rowsPerColumn = ceil(count($this->values) / $this->renderInColumns);
            }
            
            $columns = array(); // array to collect the values per column in
            $classesAtStart = $this->classes;
            foreach ($this->values as $i => $value)
            {
                $checked = ($value == $this->selected) ? ' checked="checked"' : '';
                $id = $this->id.'-'.$this->toValidHtmlId($value);
                
                // add category class if it exists
                if (isset($this->categories[$i]))
                {
                    $this->addClass(str_replace(' ', '', $this->categories[$i]));
                }
                
                $radio = '<input'
                        . $this->renderType()
                        . ' id="'.$id.'"'
                        . $this->renderClass()
                        . $this->renderName()
                        . ' value="'.$value.'"'
                        . $checked
                        . $this->renderDisabled()
                        . $this->renderTitle()
                        . $this->renderDataAttributes()
                        . $this->renderRequired()
                        . ' />'."\n";
                
                if (! empty($this->labels[$i]))
                {
                    $radio .= '<label for="'.$id.'">'.$this->labels[$i].'</label>'."\n";
                }
                
                // render in columns per category, per given columns number, or don't render in columns
                if (! empty($this->categories[$i]))
                {
                    $columns[$this->categories[$i]][] = $radio;
                }
                elseif (! empty($this->renderInColumns))
                {
                    $columns[(int) floor($i / $rowsPerColumn)][] = $radio;
                }
                else
                {
                    $output .= $radio;
                }
                
                // line end
                if (! empty($this->lineEnd))
                    $output .= $this->lineEnd;
                
                // reset classes, so the last entry doesn't have all category classes :)
                $this->classes = $classesAtStart;
            }
            
            // render in columns
            if (! empty($columns))
            {
                $output = '<table><tr>';
                foreach ($columns as $key => $col)
                {
                    $output .= '<td valign="top">';
                    
                    // add category name
                    if (! empty($this->categories))
                        $output .= '<label class="input_category">'.$key."</label><br/>\n";
                    
                    foreach ($col as $radio)
                    {
                        $output .= $radio."<br/>\n";
                    }
                    
                    $output .= '</td>';
                }
                
                $output .= '</tr></table>'."\n";
            }
            
            $output = $this->renderLabel().'<div id="'.$this->id.'-container"'.$this->renderStyle().'>'."\n".$output."</div>\n";
        }
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
    /**
     * give amount of columns the list should be displayed in
     * @param int $int
     */
    public function setRenderInColumns($int)
    {
        if ($this->validator->validate($int, 'numeric'))
        {
            $this->renderInColumns = $int;
        }
        
        return $this;
    }
    
    /**
     * add given html code at the end of each radio line
     * @param string $lineEnd
     */
    public function setLineEnd($lineEnd)
    {
        $this->lineEnd = $lineEnd;
        
        return $this;
    }
    
}
