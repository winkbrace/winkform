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
    /**
     * @var Dropdown[]
     */
    protected $dropdowns = array();

    /**
     * @var array
     */
    protected $data = array();


    /**
     * render the hidden input element
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        $output = "<ul>\n";
        foreach ($this->dropdowns as $dropdown)
            $output .= '<li>'.$dropdown->render()."</li>\n";
        $output .= "</ul>\n";

        $output .= $this->renderInvalidations();

        // create the javascript like: $("#series").chainedTo("#mark"); \n $("#model").chainedTo("#series");
        $output .= '<script>'.PHP_EOL;
        $output .= '$(document).ready(function() {'.PHP_EOL;

        for ($i = 1; $i < count($this->dropdowns); $i++)
        {
            $output .= '    $("#'.$this->dropdowns[$i]->getId().'").chainedTo("#'.$this->dropdowns[$i - 1]->getId().'");'.PHP_EOL;
        }

        $output .= '});'.PHP_EOL;
        $output .= '</script>'.PHP_EOL;

        return $output;
    }

    /**
     * set values for dropdowns by passing data array
     * This must be a database table like array with row indexes as first level keys
     * Typically the result of a query fetch has this form ;)
     * @param array $data
     * @return \WinkForm\Input\ChainedDropdowns
     * @throws \Exception
     */
    public function setData(array $data)
    {
        // allow empty
        if (empty($data))
            return $this;

        // validate
        if (empty($data[0]))
            throw new \Exception('Invalid array $data passed to setData at '.__CLASS__);
        if (! empty($data[1]) && array_keys($data[0]) != array_keys($data[1]))
            throw new \Exception('Invalid array $data passed to setData at '.__CLASS__);

        $this->data = $data;

        $this->createDropdowns();

        return $this;
    }

    /**
     * create the dropdowns based on the previously set query or result array
     * @throws \Exception
     */
    protected function createDropdowns()
    {
        if (empty($this->data))
            throw new \Exception('Error creating dropdowns, because there are no results to create dropdowns from.');

        // the separator we use to glue the values of the different columns together to ensure uniqueness
        $separator = '_';

        // collect all values with their parent value into $options
        $options = array();
        foreach ($this->data as $row)
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
     * @return Dropdown[] $dropdowns
     */
    public function getDropdowns()
    {
        return $this->dropdowns;
    }

    /**
     * Override the default implementation to store posted values
     * @see Input::setPosted()
     */
    protected function setPosted()
    {
        $dropdowns = $this->getDropdowns();
        $dropdownIds = array();

        if ($this->isPosted() && ! empty($dropdowns))
        {
            foreach ($dropdowns as $dropdown)
                $dropdownIds[$dropdown->getId()] = 'FILTER_SANITIZE_FULL_SPECIAL_CHARS';

            $post = filter_input_array(INPUT_POST, $dropdownIds);

            $this->posted = $post;
            $this->selected = $post;  // so we can always retrieve the selected fields with getSelected()
        }

        return $this;
    }

    /**
     * Override the default implementation if this input element is posted
     * @see Input::isPosted()
     * @return boolean
     */
    public function isPosted()
    {
        if (empty($this->dropdowns))
            return false;

        return $this->dropdowns[0]->isPosted();
    }

}
