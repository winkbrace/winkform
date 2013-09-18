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
        
        $expected = array('text' => array('data' => null, 'rules' => array('required', 'min:5'), 'message' => 'This is a message'));
        $this->assertEquals($expected, $this->validator->getValidations(), 'addValidation() adds an input and validation rule to it\'s $validations array.');
        
        $this->validator->addValidation($input, array('alpha_dash', 'between:4,8', 'required'));
        $this->assertCount(1, $this->validator->getValidations(), 'second call to addValidation() on same input should result in 1 entry in the validations array');
        
        $expected = array('text' => array(
            'data' => null,
            'rules' => array('required', 'min:5', 'alpha_dash', 'between:4,8'),
            'message' => 'This is a message'
            ));
        $this->assertEquals($expected, $this->validator->getValidations(), 'second call to addValidation() on same input should merge rules');
    }
    
    /**
     * @expectedException         Exception
     * @expectedExceptionMessage  Invalid rule "invalid_rule" specified.
     */
    public function testInvalidRule()
    {
        $input = Form::text('text', 'value');
        $this->validator->addValidation($input, 'invalid_rule');
    }
    
    /**
     * test simple validation
     */
    public function testValidate()
    {
        $result = $this->validator->validate('test@domain.com', 'email');
        $this->assertTrue($result, 'validate() should validate correct entry');
    }

}
