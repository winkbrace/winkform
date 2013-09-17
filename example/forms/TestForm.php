<?php

class TestForm extends WinkForm\Form
{
    public $testCheckbox,
            $testDate,
            $testFile,
            
            $submitButton;
    

    /**
     * create Test Form
     */
    function __construct()
    {
        parent::__construct();
        
        $this->testCheckbox = self::checkbox('testCheckbox')->setLabel('Choose one, two or three')->appendOptions(array(
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
            ));
        
        $this->testDate = self::date('testDate', date('d-m-Y'))->setLabel('Pick a date');
        
        $this->testFile = self::file('testFile')->setLabel('Select a file');
        
        $this->submitButton = self::submit('submitButton', 'Send')->setLabel('&nbsp;');
    }
    
    /**
     * (non-PHPdoc)
     * @see \WinkForm\Form::render()
     */
    public function render()
    {
        return $this->renderFormHead()
        
            . $this->testCheckbox->render() . BRCLR . BRCLR
            . $this->testDate->render() . BRCLR . BRCLR
            . $this->testFile->render() . BRCLR . BRCLR
            
            . $this->submitButton->render() . BRCLR
            
            . $this->renderFormFoot()
        ;
    }
    
    /**
     * (non-PHPdoc)
     * @see \WinkForm\Form::isPosted()
     */
    public function isPosted()
    {
        return $this->submitButton->isPosted();
    }

}
