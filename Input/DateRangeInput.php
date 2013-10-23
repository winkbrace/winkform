<?php namespace WinkForm\Input;

/**
 * Create 2 date input fields for selecting a date range
 *
 * @author b-deruiter
 */
class DateRangeInput extends Input
{
    /**
     * @var DateInput
     */
    protected $dateFrom;

    /**
     * @var DateInput
     */
    protected $dateTo;


    /**
     * construct DateRange object
     *
     * @param string $name
     * @param string $from  date in d-m-Y format
     * @param string $to    date in d-m-Y format
     */
    function __construct($name, $from = null, $to = null)
    {
        parent::__construct($name);

        // create the two date input fields
        $this->setDateFrom(new DateInput($this->name.'-from', $from));
        $this->setDateTo(new DateInput($this->name.'-to', $to));

        $this->attachObserver($this->dateFrom);
        $this->attachObserver($this->dateTo);

        // set default labels
        $this->setLabels(array('Between', 'and'));
    }

    /**
     * render the date range input fields
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        // render the date range input fields
        $output = $this->dateFrom->setRenderWithLabel(true)->render() . $this->dateTo->setRenderWithLabel(true)->render();

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * set labels for the Dates
     * @param array $labels (from, to)
     * @param null  $flag
     * @return $this
     */
    public function setLabels($labels, $flag = null)
    {
        $this->dateFrom->setLabel($labels[0], $flag);
        $this->dateTo->setLabel($labels[1], $flag);

        return $this;
    }

    /**
     * get the labels of the dates
     * @return array (from, to)
     */
    public function getLabels()
    {
        return array($this->dateFrom->label, $this->dateTo->label);
    }

    /**
     * Set initial values for the date range input fields
     * @param array $selected  array('from', 'to')
     * @param int $flag
     * @return $this
     */
    public function setSelected($selected, $flag = 0)
    {
        if ($this->validate($selected, 'array|size:2', 'Invalid $selected given for DateRangeInput. Has to be array of 2.'))
        {
            if (empty($this->posted) || $this->isFlagSet($flag, self::INPUT_OVERRULE_POST))
            {
                $from = array_key_exists('from', $selected) ? $selected['from'] : $selected[0];
                $to = array_key_exists('to', $selected) ? $selected['to'] : $selected[1];
                $this->dateFrom->setSelected($from);
                $this->dateTo->setSelected($to);
            }
        }

        return $this;
    }

    /**
     * @return DateInput $dateFrom
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @return DateInput $dateTo
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param DateInput $dateFrom
     * @return $this
     */
    public function setDateFrom(DateInput $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * @param DateInput $dateTo
     * @return $this
     */
    public function setDateTo(DateInput $dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * is the DateRangeInput element posted?
     *
     * @see \WinkForm\Input\Input::isPosted()
     * @return boolean
     */
    public function isPosted()
    {
        return ($this->dateFrom->isPosted() || $this->dateTo->isPosted());
    }

}
