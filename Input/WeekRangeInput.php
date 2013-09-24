<?php namespace WinkBrace\WinkForm\Input;

/**
 * Create 2 week input fields for selecting a week range
 *
 * @author b-deruiter
 */
class WeekRangeInput extends Input
{
    // the 2 week objects
    protected $weekFrom,
              $weekTo;
    
    protected $validator;
    
    
    /**
     * construct WeekRange object
     *
     * @param string $name
     * @param iyyy-iw $from
     * @param iyyy-iw $to
     */
    function __construct($name, $from = null, $to = null)
    {
        $this->validator = new \WinkBrace\WinkForm\Validation\Validator();
        
        $this->name = $name;
        
        $this->setWeekFrom(new WeekInput($name.'-from', $from));
        $this->setWeekTo(new WeekInput($name.'-to', $to));
        
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

        // copy attributes from WeekRange to the WeekInputs
        $excludes = array('name','id','value','values','label','labels','selected','posted','required','invalidations','width');
        copySharedAttributes($this->weekFrom, $this, $excludes);
        copySharedAttributes($this->weekTo, $this, $excludes);
        
        // render the week range dropdowns
        $output = $this->weekFrom->render() . '&nbsp;t/m&nbsp; ' . $this->weekTo->render();
        
        $output .= $this->renderInvalidations();
            
        return $output;
    }
    
    /**
     * set labels for the Weeks
     *
     * @param array $labels array(from, to)
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
     * Otherwise the childs will also have the same width as the parent
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
    }
    
}
