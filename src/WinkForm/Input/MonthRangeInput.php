<?php namespace WinkForm\Input;

/**
 * Create 2 month input fields for selecting a month range
 *
 * @author b-deruiter
 */
class MonthRangeInput extends Input
{
    /** @var MonthInput */
    protected $monthFrom;

    /** @var MonthInput */
    protected $monthTo;


    /**
     * construct MonthRange object
     *
     * @param string $name
     * @param yyyy-mm $from
     * @param yyyy-mm $to
     */
    function __construct($name, $from = null, $to = null)
    {
        parent::__construct($name);

        $this->setMonthFrom(new MonthInput($name.'-from', $from));
        $this->setMonthTo(new MonthInput($name.'-to', $to));

        $this->attachObserver($this->monthFrom);
        $this->attachObserver($this->monthTo);

        // set default labels
        $this->setLabels(array('Between', 'and'));
    }

    /**
     * render the month range dropdown fields
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        // render the month range dropdowns
        $output = $this->monthFrom->render() . $this->monthTo->render();

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * set labels for the Months
     * @param array $labels  array(from, to)
     * @param null  $flag
     * @return void|Input
     */
    public function setLabels($labels, $flag = null)
    {
        $this->monthFrom->setLabel($labels[0], $flag);
        $this->monthTo->setLabel($labels[1], $flag);
    }

    /**
     * @return MonthInput $monthFrom
     */
    public function getMonthFrom()
    {
        return $this->monthFrom;
    }

    /**
     * @return MonthInput $monthTo
     */
    public function getMonthTo()
    {
        return $this->monthTo;
    }

    /**
     * @param MonthInput $monthFrom
     */
    public function setMonthFrom(MonthInput $monthFrom)
    {
        $this->monthFrom = $monthFrom;
    }

    /**
     * @param MonthInput $monthTo
     */
    public function setMonthTo(MonthInput $monthTo)
    {
        $this->monthTo = $monthTo;
    }

    /**
     * (non-PHPdoc)
     * @see \WinkForm\Input\Input::isPosted()
     */
    public function isPosted()
    {
        return ($this->monthFrom->isPosted() || $this->monthTo->isPosted());
    }

}
