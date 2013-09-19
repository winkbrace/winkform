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

    /**
     * @var \WinkForm\Validator
     */
    protected $validator;

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_before()
     */
    protected function _before()
    {
        $this->validator = new Validator();
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
        $result = $this->validator->validate('this is not numeric', 'numeric');
        $this->assertFalse($result, 'validate() should invalidate incorrect test');

        // we are not only checking that a correct test passes, but also that the Validator doesn't remember a
        // negative state from before (we might accidentally build something like that in the future)
        $result = $this->validator->validate('test@domain.com', 'email');
        $this->assertTrue($result, 'validate() should validate correct entry');
    }

    /**
     * test passes() and getErrors()
     */
    public function testPasses()
    {
        // setup input to test
        $_POST['text'] = 'value';  // first create the POST value, because Input->$posted is set on construction.
        $input = Form::text('text');
        $this->validator->addValidation($input, 'required|min:5|between:4,8', 'This is a message');

        $result = $this->validator->passes();
        $this->assertTrue($result, 'passes() should return true when all rules will validate');

        $errors = $this->validator->getErrors();
        $this->assertEmpty($errors, 'errors() should return empty array when all rules validated');

        // now add validations that will fail
        $this->validator->addValidation($input, 'numeric|date');
        $this->assertFalse($this->validator->passes(), 'passes() should return false when not all rules validate');

        $errors = $this->validator->getErrors();
        $this->assertCount(2, $errors['text'], 'errors should contain 2 errors because 2 validations failed');
    }

    /**
     * test that error messages fetch the language file
     */
    public function testMessages()
    {
        // create failing validation
        $input = Form::text('text');
        $this->validator->addValidation($input, 'required');
        $this->validator->passes();

        $errors = $this->validator->getErrors();
        $this->assertArrayHasKey('text', $errors, 'errors array should have input element name as key');
        $error = $errors['text'][0];

        $this->assertEquals("The text field is required.", $error, 'The error message should display from the lang file');
    }


}
