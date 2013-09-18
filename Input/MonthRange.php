<?php namespace WinkForm\Input;

/**
 * Create 2 month input fields for selecting a month range
 *
 * @author b-deruiter
 */
class MonthRange extends Input
{
    // the 2 month objects
    protected $monthFrom,
              $monthTo;
    
    protected $validator;
    
    
    /**
     * construct MonthRange object
     *
     * @param string $name
     * @param yyyy-mm $from
     * @param yyyy-mm $to
     */
    function __construct($name, $from = null, $to = null)
    {
        $this->validator = new Validate();
        
        $this->name = $name;
        
        $this->setMonthFrom(new MonthInput($name.'-from', $from));
        $this->setMonthTo(new MonthInput($name.'-to', $to));
        
        // set default labels
        $this->setLabels(array('Between', 'and'));
    }
    
    /**
     * render the month range dropdown fields
     */
    public function render()
    {
        // default validity check
        if (! $this->validator->isValid())
            throw new \Exception($this->validator->getMessage('Error rendering '.get_class($this).' object with name '.$this->name));
        
        // copy attributes from MonthRange to the MonthInputs
        $excludes = array('name','id','value','values','label','labels','selected','posted','required','invalidations');
        copySharedAttributes($this->monthFrom, $this, $excludes);
        copySharedAttributes($this->monthTo, $this, $excludes);
        
        // render the month range dropdowns
        $output = $this->monthFrom->render() . '&nbsp;t/m&nbsp; ' . $this->monthTo->render();
        
        $output .= $this->renderInvalidations();
        
        return $output;
    }
    
    /**
     * set labels for the Months
     *
     * @param array $labels array(from, to)
     */
    public function setLabels($labels, $flag = null)
    {
        $this->monthFrom->setLabel($labels[0], $flag);
        $this->monthTo->setLabel($labels[1], $flag);
    }
    
    /**
     * @return the $monthFrom
     */
    public function getMonthFrom()
    {
        return $this->monthFrom;
    }

    /**
     * @return the $monthTo
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
    
}
