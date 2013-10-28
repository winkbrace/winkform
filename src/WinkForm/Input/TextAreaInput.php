<?php namespace WinkForm\Input;

class TextAreaInput extends Input
{
    protected $type = 'textarea';
    protected $rows;
    protected $cols;
    protected $jsCounter;            // If we should show a JS counter next to a textarea
    protected $jsCounterOptions;     // Options for the JS counter

    /**
     * render the hidden input element
     * @param boolean $echo return as string (true) or echo (false)
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        $output = $this->renderLabel()
                    . '<textarea'
                    . $this->renderId()
                    . $this->renderClass()
                    . $this->renderName()
                    . $this->renderRows()
                    . $this->renderCols()
                    . $this->renderStyle()
                    . $this->renderDisabled()
                    . $this->renderTitle()
                    . $this->renderDataAttributes()
                    . $this->renderRequired()
                    . $this->renderAutoFocus()
                    . '>' . PHP_EOL
                    . $this->getValue()
                    . '</textarea>' . PHP_EOL;

        // add the JS counter if needed
        $output .= $this->renderCounter();

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * @return string $rows
     */
    public function renderRows()
    {
        return ! empty($this->rows) ? ' rows="'.$this->rows.'"' : '';
    }

    /**
     * @return string $cols
     */
    public function renderCols()
    {
        return ! empty($this->cols) ? ' cols="'.$this->cols.'"' : '';
    }

    /**
     * Builds the HTML and JS needed for the characters left/used.
     *
     * @return string containing the HTML and JS to show the number of
     * characters used/left. If there is no counter it will return
     * an empty string.
     */
    public function renderCounter()
    {
        if (! $this->jsCounter)
            return '';

        if ($this->isDisabled())
        {
            $output = 'You have used <span class="done" id="' . $this->getCounterID() . '">'
                        . strlen($this->getValue())
                        .'</span> characters.';
        }
        else
        {
            $prefix = 'You have ';
            $suffix = ' characters left.';

            $output = $prefix;
            $output .= '<span id="' . $this->getCounterID() . '"';

            // add the options for the counter as a data-value attribute
            $output .= ' data-options="' . $this->getCounterOptions() . '"';

            $output .= '></span>';
            $output .= $suffix . PHP_EOL;
        }

        $output = '<p class="counter">' . $output . '</p>';

        // add the JS that does the counting
        if (! $this->isDisabled())
            $output .= $this->getCounterJS() . PHP_EOL;

        return $output;
    }

    /**
     * Generates a unique counter ID so it doesn't conflict.
     * @return string
     */
    protected function getCounterID()
    {
        return $this->getId() . '_counter';
    }

    /**
     * Prepares the array of options for the counter plugin.
     *
     * @return string a HTML escaped JSON with the options for the counter plugin.
     * Contains at least the counter ID.
     */
    protected function getCounterOptions()
    {
        $options = $this->jsCounterOptions;

        // always provide a jQuery selecter for the counter
        $counterId = array_key_exists('counter', $options)
            ? $options['counter']        // custom jQuery selector
            : $this->getCounterID();     // default jQuery selecter (assumes the textarea has a unique ID)

        $options['counter'] =  '#' . ltrim($counterId, '#');

        $jsonOptions = htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8');

        return $jsonOptions;
    }

    /**
     * The JS to initialize a counter for a textarea
     * @return string the JS script
     */
    protected function getCounterJS()
    {
        return '<script type="text/javascript" charset="utf-8">
        $(document).ready(function()
        {
            var options = $("#' . $this->getCounterID() . '").data("options");
            $("#' . $this->getId() . '").simplyCountable(options);
        });
        </script>';
    }

    /**
     * @param int $rows
     */
    public function setRows($rows)
    {
        if ($this->validate($rows, 'numeric'))
        {
            $this->rows = $rows;
        }

        return $this;
    }

    /**
     * @param int $cols
     */
    public function setCols($cols)
    {
        if ($this->validate($cols, 'numeric'))
        {
            $this->cols = $cols;
        }

        return $this;
    }

    /**
     * @param string $wrapStyle
     */
    public function setWrapStyle($wrapStyle)
    {
        if ($this->validate($wrapStyle, 'in:normal,pre,nowrap,pre-wrap,pre-line,inherit', 'Given textarea wrap-style "'.$wrapStyle.'" is invalid'))
        {
            // remove old white-space style
            foreach ($this->styles as $style)
            {
                if (preg_match('/^white-space:/', $style)) // regular expression to make sure the string starts with white-space
                    $this->removeStyle($style);
            }
            // add new white-space style
            $this->addStyle('white-space:'.$wrapStyle);
        }

        return $this;
    }

    /**
     * Set a JS counter for a textarea.
     *
     * @link https://github.com/aaronrussell/jquery-simply-countable/
     * @param boolean $counter true to enable it, false means a counter will not be added
     * @param array $options An associative array of additional options. Defaults to an empty array. It may contain the following elements:
     * <ul>
     *   <li><code>counter</code> - A jQuery selector to match the 'counter' element. Defaults to <code>#counter</code>.</li>
     *   <li><code>countType</code> - Select whether to count <code>characters</code> or <code>words</code>. Defaults to <code>characters</code>.</li>
     *   <li><code>wordSeparator</code> - The word separator when counting <code>words</code>. Defaults to white-space.</li>
     *   <li><code>maxCount</code> - The maximum character (or word) count of the text input or textarea. Defaults to <code>140</code>.</li>
     *   <li><code>strictMax</code> - Prevents the user from being able to exceed the <code>maxCount</code>. Defaults to <code>false</code>.</li>
     *   <li><code>countDirection</code> - Select whether to count <code>down</code> or <code>up</code>. Defaults to <code>down</code>.</li>
     *   <li><code>safeClass</code> - The CSS class applied to the counter element when it is within the maxCount figure. Defaults to <code>safe</code>.</li>
     *   <li><code>overClass</code> - The CSS class applied to the counter element when it exceeds the maxCount figure. Defaults to <code>over</code>.</li>
     *   <li><code>thousandSeparator</code> - The separator for multiples of 1,000. Set to <code>false</code> to disable. Defaults to <code>,</code>.</li>
     *   <li><code>onOverCount</code> - Callback function called when counter goes over <code>maxCount</code> figure.</li>
     *   <li><code>onSafeCount</code> - Callback function called when counter goes below <code>maxCount</code> figure.</li>
     *   <li><code>onMaxCount</code> - Callback function called when in <code>strictMax</code> mode and counter hits <code>maxCount</code> figure.</li>
     * </ul>
     * @return TextAreaInput
     */
    public function setCounter($counter, array $options = array())
    {
        if ($this->validate($counter, 'boolean'))
        {
            $this->jsCounter = $counter;

            if ($options)
            {
                $allowedParams = array('counter', 'countType', 'wordSeparator', 'maxCount', 'strictMax',
                                       'countDirection', 'safeClass', 'overClass', 'thousandSeparator',
                                       'onOverCount', 'onSafeCount', 'onMaxCount');
                $this->jsCounterOptions = array_intersect_key($options, array_flip($allowedParams));
            }
        }

        return $this;
    }

}
