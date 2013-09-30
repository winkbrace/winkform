<?php namespace WinkForm\Input;

use WinkForm\Validation\QuickValidator;

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
    function __construct($name, $from, $to)
    {
        $this->validator = new QuickValidator();

        $this->name = $name;

        // create the two date input fields
        $this->setDateFrom(new DateInput($this->name.'-from', $from));
        $this->setDateTo(new DateInput($this->name.'-to', $to));

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

        // via casting we can pass all attributes that were set on DateRane down to the DateInput fields
        $excludes = array('type', 'name', 'id', 'value', 'label', 'selected', 'posted', 'required', 'invalidations', 'inReportForm');
        copySharedAttributes($this->dateFrom, $this, $excludes);
        copySharedAttributes($this->dateTo, $this, $excludes);

        // render the date range input fields
        $output = $this->dateFrom->render() . $this->dateTo->render();

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * set labels for the Dates
     * @param array $labels (from, to)
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
                // of flag is niet geset of wel geset maar dan moet POST empty zijn
                if (! $this->isFlagSet($flag, self::INPUT_SELECTED_INITIALLY_ONLY) || empty($_POST))
                {
                    $from = array_key_exists('from', $selected) ? $selected['from'] : $selected[0];
                    $to = array_key_exists('to', $selected) ? $selected['to'] : $selected[1];
                    $this->dateFrom->setSelected($from);
                    $this->dateTo->setSelected($to);
                }
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

}
