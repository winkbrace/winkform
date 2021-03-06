<?php namespace WinkForm\Input;

class WeekInput extends Input
{
    // the text input fields
    protected $year,
              $week,
              $hiddenInput; // $hidden already exists in Input


    /**
     * construct Input
     * @param string $name
     * @param string $week
     */
    function __construct($name, $week = null)
    {
        parent::__construct($name, $week);

        // create week and year dropdown objects
        $this->year = new Dropdown($this->name.'-year');
        $this->year
            ->setValues(range(date('Y') - 3, date('Y') + 1))
            ->setLabels(range(date('Y') - 3, date('Y') + 1))
            ->setSelected(date('Y'))
            ->setWidth(65);

        // create array with week numbers from 01 to 53
        $weeks = range(1, 53);
        array_walk($weeks, function(&$nr) {
            $nr = str_pad($nr, 2, '0', STR_PAD_LEFT);
        });

        $this->week = new Dropdown($this->name.'-week');
        $this->week->setValues($weeks)
                    ->setLabels($weeks)
                    ->setSelected(date('W'))
                    ->setWidth(52);

        $this->attachObserver($this->year);
        $this->attachObserver($this->week);

        $this->addClass('week-dropdown');

        // this will be the field we will actually fetch when posted, because that's easier. ;)
        // we need javascript to fill it when the dropdowns change, tho.
        $this->hiddenInput = new HiddenInput($name, date('Y-W'));

        if (! empty($week))
            $this->setSelected($week);

        // store posted as selected
        $this->setPosted();
    }


    /**
     * @param string $selected
     * @param int $flag
     * @return $this
     */
    public function setSelected($selected, $flag = 0)
    {
        if (empty($this->posted) || $this->isFlagSet($flag, self::INPUT_OVERRULE_POST))
        {
            // only continue if valid week string is given
            // do not use 'date_format:o-W' since date_parse_from_format() does not support this format
            if ($this->validate($selected, 'regex:/^\d{4}-\d{2}$/', 'Invalid week string given for :attribute. Needs to be format IYYY-IW.'))
            {
                list($year, $week) = explode('-', $selected);
                $this->validate($week, 'numeric|between:1,53');
                $this->validate($year, 'numeric|between:1900,2200');

                if ($this->validator->isValid())
                {
                    $this->week->setSelected($week);
                    $this->year->setSelected($year);
                    $this->hiddenInput->setValue($selected);
                }
            }
        }

        return $this;
    }


    /**
     * render the date input element
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        $output = '';

        // start output
        if (! empty($this->label) && $this->renderWithLabel)
            $output .= '<label for="'.$this->id.'">'.$this->label.'</label> ';

        // The applied styles will only apply to the container
        $output .= '<span id="'.$this->id.'-container" class="inputs-container"'.$this->renderStyle().'>';
        $output .= $this->year->render();
        $output .= $this->week->render();
        $output .= $this->hiddenInput->render();
        $output .= "</span>\n";

        // javascript to change the hidden input field when a dropdown changes
        // I create a trigger by id, so when there are multiple WeekInputs on the screen these scripts wont all fire every time one changes
        $output .= '<script type="text/javascript">
                        // fill hidden input field when one of the dropdowns changes
                        $("#'.$this->name.'-year, #'.$this->name.'-week").change(function() {
                            $("#'.$this->name.'").val($("#'.$this->name.'-year").val() + "-" + $("#'.$this->name.'-week").val());
                        });
                    </script>'."\n";

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * The width should be split between the two WeekInputs
     *
     * Otherwise the childs will also have the same width as the parent
     * which is not logical.
     *
     * @see Input::setWidth()
     */
    public function setWidth($width)
    {
        parent::setWidth($width);

        $yearWidth = round($width * 0.55, 0, PHP_ROUND_HALF_DOWN);

        $this->year->setWidth($yearWidth);            // 55% width to the year
        $this->week->setWidth($width - $yearWidth);   // 45% width to the week
    }

}
