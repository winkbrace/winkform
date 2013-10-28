<?php namespace WinkForm\Input;

class MonthInput extends Input
{

    protected $month,
              $year,
              $monthyear; // hidden input field with the name $name that will contain the YYYY-MM value


    /**
     * construct MonthInput object
     *
     * @param string $name
     * @param yyyy-mm $value
     */
    function __construct($name, $value = null)
    {
        parent::__construct($name, $value);

        // create the two dropdowns
        $this->month = new Dropdown($name.'-month', date('m'));
        $this->month->setWidth(92);
        $months = array(1 => 'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December');
        foreach ($months as $nr => $label)
            $this->month->appendOption($nr, $label);

        $this->year = new Dropdown($name.'-year', date('Y'));
        $this->year->setWidth(65);
        for ($y = date('Y') - 2; $y < date('Y') + 3; $y++)
            $this->year->appendOption($y, $y);

        $this->monthyear = new HiddenInput($name, date('Y').'-'.date('m'));

        $this->attachObserver($this->month);
        $this->attachObserver($this->year);

        if (! empty($value))
            $this->setSelected($value);
    }

    /**
     * render the date range input fields
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        $output = '<span id="'.$this->id.'-container" class="inputs-container"'.$this->renderStyle().'>';

        // render the date range input fields
        $output .= $this->renderLabel()
            . $this->month->render()
            . $this->year->render()
            . $this->monthyear->render();

        $output .= "</span>\n";

        $output .= $this->renderInvalidations();

        $output .= '<script type="text/javascript">
                     $(document).ready(function()
                     {
                        // fill hidden input field when one of the dropdowns changes
                        $("#'.$this->name.'-year, #'.$this->name.'-month").change(function() {
                            $("#'.$this->name.'").val($("#'.$this->name.'-year").val() + "-" + $("#'.$this->name.'-month").val());
                        });
                      });
                    </script>' . PHP_EOL;

        return $output;
    }

    /**
     * set a value selected
     *
     */
    public function setSelected($selected, $flag = 0)
    {
        // validate
        if (! $this->validate($selected, 'size:7|date_format:Y-m'))
            throw new \Exception($this->renderValidationErrors("Invalid string given for setting ".get_class($this)." object as selected: $selected. Has to be YYYY-MM."));

        list($year, $month) = explode('-', $selected);
        $this->validate($year, 'numeric|between:1000,9999');
        $this->validate($month, 'numeric|between:1,12');

        if (! $this->validator->isValid())
            throw new \Exception($this->renderValidationErrors('Error setting selected values for '.get_class($this).' object with name '.$this->name));

        // set selected
        $this->month->setSelected($month, $flag);
        $this->year->setSelected($year, $flag);
        $this->monthyear->setSelected($selected, $flag);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \WinkForm\Input\Input::getSelected()
     */
    public function getSelected()
    {
        return $this->monthyear->getSelected();
    }

    /**
     * (non-PHPdoc)
     * @see \WinkForm\Input\Input::getPosted()
     */
    public function getPosted()
    {
        // make sure the date is in yyyy-mm format, especially for single digit months
        return $this->year->getPosted() . '-' . str_pad($this->month->getPosted(), 2, 0, STR_PAD_LEFT);
    }

}
