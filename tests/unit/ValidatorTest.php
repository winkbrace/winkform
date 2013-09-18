<?php

use Codeception\Util\Stub;
use WinkForm\Validator;
use WinkForm\Form;

class ValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;
    
    protected $validator;

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_before()
     */
    protected function _before()
    {
        $this->validator = Validator::getInstance();
    }

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_after()
     */
    protected function _after()
    {
        unset($this->validator);
    }

    /**
     * create Validator object
     */
    public function testCreation()
    {
        $this->assertInstanceOf('WinkForm\Validator', $this->validator, 'getInstance() returns the Validator object');
    }
    
    /**
     * test addValidation
     */
    public function testAddValidation()
    {
        $input = Form::text('text', 'value');
        $this->validator->addValidation($input, 'required|min:5', 'This is a message');
        
        $expected = array('text' => array('data' => null, 'rules' => 'required|min:5', 'message' => 'This is a message'));
        $this->assertEquals($expected, $this->validator->getValidations());
    }

}
