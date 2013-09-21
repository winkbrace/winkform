<?php namespace WinkBrace\WinkForm\Input;

/**
 * Create 2 date input fields for selecting a date range
 *
 * @author b-deruiter
 */
class DateRange extends Input
{
    // the 2 date objects
    protected $dateFrom,
              $dateTo;
    
    
    /**
     * construct DateRange object
     *
     * @param string $name
     * @param date-string $from
     * @param date-string $to
     */
    function __construct($name, $from, $to)
    {
        $this->validator = new \WinkBrace\WinkForm\Validator();
        
        $this->name = $name;
        
        if (! empty($from))
            $this->validator->date($from);
        if (! empty($to))
            $this->validator->date($to);
        
        if (! $this->validator->passes())
            throw new \Exception($this->validator->getMessage('Error creating '.get_class($this).' object with name '.$this->name));
        
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
     *
     * @param array $labels (from, to)
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
     */
    public function setDateFrom(DateInput $dateFrom)
    {
        $this->dateFrom = $dateFrom;
        
        return $this;
    }

    /**
     * @param DateInput $dateTo
     */
    public function setDateTo(DateInput $dateTo)
    {
        $this->dateTo = $dateTo;
        
        return $this;
    }
    
}
