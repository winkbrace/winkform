<?php

use WinkForm\Form;

/**
 * @author b-deruiter
 *
 */
class TestForm extends Form
{
    // For this test case I want to keep it simple and give the objects the same name as the class.
    // Typically you would give the input objects the same name as the input elements.

    // inputs
    public $oAddress,
            $oChained,
            $oCheckbox,   // single
            $oCheckboxes, // multiple
            $oColor,
            $oDate,
            $oDateRange,
            $oDropdown,
            $oEmail,
            $oFile,
            $oHidden,
            $oMonth,
            $oMonthRange,
            $oNumber,
            $oPassword,
            $oRadio,
            $oRange,
            $oSearch,
            $oTel,
            $oText,
            $oTextarea,
            $oUrl,
            $oWeek,
            $oWeekRange;

    // buttons
    public $oButton,
            $oImage,
            $oReset,
            $oSubmit;


    /**
     * create the form
     */
    function __construct()
    {
        parent::__construct();

        $this->oAddress = self::address('address');
        $this->oChained = self::chainedDropdowns('chained');
        $this->oCheckbox = self::checkbox('checkbox', 'value');
        $this->oCheckboxes = self::checkbox('checkboxes')->appendOptions(array(1 => 'one','two','three'));
        $this->oColor = self::color('color');
        $this->oDate = self::date('date');
        $this->oDateRange = self::dateRange('dateRange', date('d-m-Y'), date('d-m-Y'));
        $this->oDropdown = self::dropdown('dropdown')->appendOptions(array(1 => 'one','two','three'));
        $this->oEmail = self::email('email', 'info@basderuiter.nl');
        $this->oFile = self::file('file');
        $this->oHidden = self::hidden('hidden');
        $this->oMonth = self::month('month');
        $this->oMonthRange = self::monthRange('monthRange', '2013-01', '2013-09');
        $this->oNumber = self::number('number');
        $this->oPassword = self::password('password');
        $this->oRadio = self::radio('radio')->appendOptions(array(1 => 'one','two','three'));
        $this->oRange = self::range('range');
        $this->oSearch = self::search('search');
        $this->oTel = self::tel('tel');
        $this->oText = self::text('text', 'value');
        $this->oTextarea = self::textarea('textarea', 'value');
        $this->oUrl = self::url('url');
        $this->oWeek = self::week('week', '2013-44');
        $this->oWeekRange = self::weekRange('weekRange', '2013-01', '2013-38');

        $this->oButton = self::inputButton('button', 'Button');
        $this->oImage = self::image('image')->setAlt('Image')->setSrc('https://2.gravatar.com/avatar/f65305395860df24db70a8dc6aeddc2f');
        $this->oReset = self::reset('reset', 'Reset');
        $this->oSubmit = self::submit('submit', 'Submit');

    }

    /**
     * (non-PHPdoc)
     * @see \WinkForm\Form::render()
     */
    public function render()
    {
        $output = $this->renderFormHead();

        $output .= $this->oAddress->render() . BRCLR
                . $this->oChained->render() . BRCLR
                . $this->oCheckbox->render() . BRCLR
                . $this->oCheckboxes->render() . BRCLR
                . $this->oColor->render() . BRCLR
                . $this->oDate->render() . BRCLR
                . $this->oDateRange->render() . BRCLR
                . $this->oDropdown->render() . BRCLR
                . $this->oEmail->render() . BRCLR
                . $this->oFile->render() . BRCLR
                . $this->oHidden->render() . BRCLR
                . $this->oMonth->render() . BRCLR
                . $this->oMonthRange->render() . BRCLR
                . $this->oNumber->render() . BRCLR
                . $this->oPassword->render() . BRCLR
                . $this->oRadio->render() . BRCLR
                . $this->oRange->render() . BRCLR
                . $this->oSearch->render() . BRCLR
                . $this->oTel->render() . BRCLR
                . $this->oText->render() . BRCLR
                . $this->oTextarea->render() . BRCLR
                . $this->oUrl->render() . BRCLR
                . $this->oWeek->render() . BRCLR
                . $this->oWeekRange->render() . BRCLR

                . $this->oButton->render() . BRCLR
                . $this->oImage->render() . BRCLR
                . $this->oReset->render() . BRCLR
                . $this->oSubmit->render() . BRCLR;

        $output .= $this->renderFormFoot();

        return $output;
    }

    /**
     * (non-PHPdoc)
     * @see \WinkForm\Form::isPosted()
     */
    public function isPosted()
    {
        return $this->oSubmit->isPosted() || $this->oImage->isPosted();
    }

}
