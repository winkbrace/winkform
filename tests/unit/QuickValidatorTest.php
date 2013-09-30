<?php

use Codeception\Util\Stub;
use WinkForm\Validation\QuickValidator;
use WinkForm\Form;
use WinkForm\Validation\ValidationException;

class QuickValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;

    /**
     * @var \WinkForm\Validation\QuickValidator
     */
    protected $validator;

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_before()
     */
    protected function _before()
    {
        $this->validator = new QuickValidator();
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
        $this->assertInstanceOf('WinkForm\Validation\QuickValidator', $this->validator);
    }

    /**
     * test simple validation
     */
    public function testValidate()
    {
        $result = $this->validator->validate('test', 'this is not numeric', 'numeric|email');
        $this->assertFalse($result, 'validate() should invalidate incorrect test');

        // we are not only checking that a correct test passes, but also that the Validator doesn't remember a
        // negative state from before (we might accidentally build something like that in the future)
        $result = $this->validator->validate('test', 'test@domain.com', 'email');
        $this->assertTrue($result, 'validate() should validate correct entry');

        // however, the Validator class should still keep track of all the validate errors
        $errors = $this->validator->getAttributeErrors('test');
        $this->assertCount(2, $errors, 'the Validator class should remember all errors');
    }

    /**
     * test that a custom message is returned when given
     */
    /*
    public function testCustomMessage()
    {
        $input = Form::text('my_name');
        $this->validator->addValidation($input, 'required', ':attribute is required.');
        $this->validator->isValid();

        $errors = $this->validator->getErrors();
        $error = $errors['my_name'][0];
        dd($error);
        $this->assertEquals("my name is required.", $error, 'The error message should display the custom error message');
    }
    */

    /**
     * test that I use the date_format check right
     */
    public function testDateFormat()
    {
        $this->validator->validate('test', '28-02-2013', 'date_format:d-m-Y');
        $this->assertTrue($this->validator->isValid(), 'date format should be the european date format');

        $this->validator->validate('test', '8-2-2013', 'date_format:d-m-Y');
        $this->assertTrue($this->validator->isValid(), 'date format is indifferent about leading zeroes');
    }

    /**
     * test validation with comma in in: list
     */
    public function testCommaInIn()
    {
        $rule = 'in:"TAB", ";", ","';
        $this->validator->validate('test', ',', $rule);
        $this->assertTrue($this->validator->isValid());
    }

    /**
     * test validation with pipe in in: list
     */
    public function testPipeInIn()
    {
        // note: when using in, not_in or regex, then you must pass an array of rules if they contain any of the special characters: | : ,
        $rule = array('in:"TAB", "|", ","');
        $this->validator->validate('test', '|', $rule);
        $this->assertTrue($this->validator->isValid());
    }

    /**
     * @expectedException \WinkForm\Validation\ValidationException
     */
    public function testValidationException()
    {
        $this->validator->validate('test', 'non numeric value', 'numeric|max:8|in:2,3,4');
        if (! $this->validator->isValid())
            throw new ValidationException('The test throws the exception', $this->validator->getErrors());

        $this->fail('The ValidationException should have been thrown.');
    }

}
