<?php namespace WinkForm\Input;

/**
 * class to create chained dropdowns <select>
 *
 * The contents of later dropdowns are depending on what is selected in earlier dropdowns
 * These dropdowns require the jquery plugin jquery.chained.min.js found at http://www.appelsiini.net/projects/chained
 *
 */
class ChainedDropdowns extends Input
{
    protected $dropdowns = array(),
              $query;
    
    
    /**
     * render the hidden input element
     */
    public function render()
    {
        // default validity check
        if (! $this->validator->isValid())
            throw new \Exception($this->validator->getMessage('Error rendering '.get_class($this).' object with name '.$this->name));
            
        $output = "<ul>\n";
        foreach ($this->dropdowns as $dropdown)
            $output .= '<li>'.$dropdown->render()."</li>\n";
        $output .= "</ul>\n";
        
        $output .= $this->renderInvalidations();
        
        // create the javascript like: $("#series").chainedTo("#mark"); \n $("#model").chainedTo("#series");
        $output .= '<script>'.PHP_EOL;
        $output .= '$(document).ready(function() {'.PHP_EOL;
        for ($i = 1; $i < count($this->dropdowns); $i++)
            $output .= '    $("#'.$this->dropdowns[$i]->getId().'").chainedTo("#'.$this->dropdowns[$i - 1]->getId().'");'.PHP_EOL;
        $output .= '});'.PHP_EOL;
        $output .= '</script>'.PHP_EOL;
        
        return $output;
    }
    
    /**
     * set values for dropdowns with a query
     * @param Query $query
     */
    public function setQuery(\Query $query)
    {
        $this->query = $query;
        $this->result = $query->fetchAll();
        
        $this->createDropdowns();
        
        return $this;
    }
    
    /**
     * set values for dropdowns by passing result array
     * @param array $result
     */
    public function setResultArray($result)
    {
        if ($this->validator->isArray($result))
            $this->result = $result;
        
        $this->createDropdowns();
        
        return $this;
    }
    
    /**
     * create the dropdowns based on the previously set query or result array
     * @throws Exception
     */
    protected function createDropdowns()
    {
        if (empty($this->result))
            throw new \Exception('Error creating dropdowns, because there are no results to create dropdowns from.');
        
        // the separator we use to glue the values of the different columns together to ensure uniqueness
        $separator = '_';
        
        // collect all values with their parent value into $options
        $options = array();
        foreach ($this->result as $row)
        {
            $class = null;
            foreach ($row as $colname => $value)
            {
                // we store $class . $separator . $value instead of just $val, because it is possible that different level1's have same level2's
                // and by collecting this way, they will not get overwritten
                // Example: [ADSL2+][1. Geen mening]  and  [Fiber][1. Geen mening]
                $title = $class . $separator . $value;
                $options[$colname][$title] = array('value' => $value, 'label' => $value, 'class' => $class);
                $class = $title; // for the next column
            }
        }
        
        $selected = $this->getSelected();
        foreach ($options as $ddName => $ddOptionsAttributes)
        {
            // create the dropdown
            $dropdown = new Dropdown($ddName);
            $dropdown->setLabel(initcap($ddName));
            $dropdown->prependOption('', '-- all --');
            $dropdown->appendOptionClass(''); // also add dummy values here
            $dropdown->appendOptionTitle('');
            
            // create the options with class and title
            foreach ($ddOptionsAttributes as $title => $attributes)
            {
                // add as option if label is not empty
                if (! empty($attributes['label']))
                {
                    $dropdown->appendOption($attributes['value'], $attributes['label']);
                    
                    // replace spaces and other invalid class characters with underscores
                    // based on the class we search for parent options with title equals that class, so title needs the same replacements
                    $dropdown->appendOptionClass($this->toValidHtmlId($attributes['class'], '_'));
                    $dropdown->appendOptionTitle($this->toValidHtmlId($title, '_'));
                }
            }
            
            // set the selected value
            if (is_array($selected) && sizeof($selected))
                $dropdown->setSelected(array_shift($selected));
            
            // add to array of dropdowns
            $this->dropdowns[] = $dropdown;
        }
        
        // refresh the posted values
        if ($this->isPosted())
            $this->setPosted();
    }

    /**
     * @return Query $query
     */
    public function getQuery()
    {
        return $this->query;
    }
    
    /**
     * @return array $dropdowns
     */
    public function getDropdowns()
    {
        return $this->dropdowns;
    }

    /**
     * Override the default implementation to store posted values
     * @see Input::setPosted()
     * @param $posted
     */
    protected function setPosted()
    {
        $dropDowns = $this->getDropdowns();
        $dropDownIds = array();
        
        if ($this->isPosted() && ! empty($dropDowns))
        {
            foreach ($dropDowns as $dropDown)
                $dropDownIds[$dropDown->getId()] = 'FILTER_SANITIZE_FULL_SPECIAL_CHARS';
            
            $post = filter_input_array(INPUT_POST, $dropDownIds);
            
            $this->posted = $post;
            $this->selected = $post;  // so we can always retrieve the selected fields with getSelected()
        }
    
        return $this;
    }
    
    /**
     * Override the default implementation if this input element is posted
     * @see Input::setPosted()
     * @return boolean
     */
    public function isPosted()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
}
