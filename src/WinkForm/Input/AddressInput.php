<?php namespace WinkForm\Input;

use WinkForm\Form;
use WinkForm\Translation\Translator;

class AddressInput extends Input
{

    protected $type = 'address';

    /**
     * @var TextInput
     */
    protected $postcode;

    /**
     * @var TextInput
     */
    protected $houseNumber;

    /**
     * @var TextInput
     */
    protected $houseNumberExtension;


    /**
     * construct AddressInput object
     * @param string $name
     * @param string $value
     */
    function __construct($name, $value = null)
    {
        parent::__construct($name, $value);

        $translator = Translator::getInstance();

        // create the text inputs
        // NOTE: values must be the same as the titles for the jquery script
        $postcode = $translator->get('inputs.postal-code');
        $this->postcode = Form::text($name.'-'.$postcode)
            ->setPlaceholder(str_replace('-', ' ', $postcode))
            ->setTitle(str_replace('-', ' ', $postcode))
            ->setWidth(100);

        $hnr = $translator->get('inputs.house-number');
        $this->houseNumber = Form::text($name.'-'.$hnr)
            ->setTitle(str_replace('-', ' ', $hnr))
            ->setPlaceholder(str_replace('-', ' ', $hnr))
            ->setWidth(50);

        $ext = $translator->get('inputs.extension');
        $this->houseNumberExtension = Form::text($name.'-'.$ext)
            ->setPlaceholder(str_replace('-', ' ', $ext))
            ->setTitle(str_replace('-', ' ', $ext))
            ->setWidth(150);

        $this->attachObserver($this->postcode);
        $this->attachObserver($this->houseNumber);
        $this->attachObserver($this->houseNumberExtension);

        // set the global placeholder style that will get copied down
        $this->addClass('address');
    }

    /**
     * render the date range input fields
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        // render the date range input fields
        $output = $this->postcode->render()
                . $this->houseNumber->render()
                . $this->houseNumberExtension->render();

        $output .= $this->renderInvalidations();

        return $output;
    }

    /**
     * setSelected method that will set the child inputs values
     * @param array $selected array(postcode, housenr, extension)
     * @param int $flag
     * @return \WinkForm\Input\AddressInput
     * @see \WinkForm\Input\Input::setSelected()
     */
    public function setSelected($selected, $flag = 0)
    {
        if (empty($this->posted) || $this->isFlagSet($flag, self::INPUT_OVERRULE_POST))
        {
            $selected = array_values((array) $selected);
            $this->selected = $selected;

            list($postcode, $housenumber, $extension) = $selected;
            $this->postcode->setSelected($postcode);
            $this->houseNumber->setSelected($housenumber);
            $this->houseNumberExtension->setSelected($extension);
        }

        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        // postcode is the first field to render, so it should have the label
        $this->postcode->setLabel($label);

        return $this;
    }

    /**
     *
     * @return \WinkForm\Input\TextInput $postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * \WinkForm\Input\TextInput $house_number
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * \WinkForm\Input\TextInput $house_number_extension
     */
    public function getHouseNumberExtension()
    {
        return $this->houseNumberExtension;
    }

    /**
     * (non-PHPdoc)
     * @see \WinkForm\Input\Input::isPosted()
     */
    public function isPosted()
    {
        $translator = Translator::getInstance();

        return ($this->postcode->isPosted()
                && $this->houseNumber->isPosted()
                && $this->postcode->getPosted() != $translator->get('inputs.postal-code')
                && $this->houseNumber->getPosted() != $translator->get('inputs.house-number')
               );
    }

}
