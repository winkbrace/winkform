<?php namespace WinkForm\Input;

/**
 * Create 2 week input fields for selecting a week range
 *
 * @author b-deruiter
 */
class WeekRangeInput extends Input
{
    /** @var WeekInput */
    protected $weekFrom;

    /** @var WeekInput */
    protected $weekTo;


    /**
     * construct WeekRange object
     *
     * @param string $name
     * @param string $from  week in iyyy-iw format
     * @param string $to    week in iyyy-iw format
     */
    function __construct($name, $from = null, $to = null)
    {
        parent::__construct($name);

        $this->setWeekFrom(new WeekInput($name.'-from', $from));
        $this->setWeekTo(new WeekInput($name.'-to', $to));

        $this->attachObserver($this->weekFrom);
        $this->attachObserver($this->weekTo);

        // set default labels
        $this->setLabels(array('Between', 'and'));
    }

    /**
     * render the week range dropdown fields
     */
    public function render($echo = false)
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        // render the week range dropdowns
        $output = $this->weekFrom->render() . $this->weekTo->render();

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * set labels for the Weeks
     * @param array     $labels
     * @param null|int  $flag
     * @return void|Input
     */
    public function setLabels($labels, $flag = null)
    {
        $this->weekFrom->setLabel($labels[0], $flag);
        $this->weekTo->setLabel($labels[1], $flag);
    }

    /**
     * @return WeekInput $weekFrom
     */
    public function getWeekFrom()
    {
        return $this->weekFrom;
    }

    /**
     * @return WeekInput $weekTo
     */
    public function getWeekTo()
    {
        return $this->weekTo;
    }

    /**
     * @param WeekInput $weekFrom
     */
    public function setWeekFrom(WeekInput $weekFrom)
    {
        $this->weekFrom = $weekFrom;
    }

    /**
     * @param WeekInput $weekTo
     */
    public function setWeekTo(WeekInput $weekTo)
    {
        $this->weekTo = $weekTo;
    }

    /**
     * The width should be split equally between the two WeekInputs
     *
     * Otherwise the children will also have the same width as the parent
     * which is not logical.
     *
     * @see Input::setWidth()
     */
    public function setWidth($width)
    {
        parent::setWidth($width);

        $childWidth = round($width / 2, 0, PHP_ROUND_HALF_DOWN);

        $this->getWeekFrom()->setWidth($childWidth);
        $this->getWeekTo()->setWidth($childWidth);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \WinkForm\Input\Input::isPosted()
     */
    public function isPosted()
    {
        return ($this->weekFrom->isPosted() || $this->weekTo->isPosted());
    }

}
