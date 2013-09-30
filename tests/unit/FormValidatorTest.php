<?php

use Codeception\Util\Stub;
use WinkForm\Validation\FormValidator;
use WinkForm\Form;

class FormValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;

    /**
     * @var \WinkForm\Validation\FormValidator
     */
    protected $validator;

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_before()
     */
    protected function _before()
    {
        $this->validator = new FormValidator();
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
        $this->assertInstanceOf('WinkForm\Validation\FormValidator', $this->validator);
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
        $this->fail('An invalid rule should throw an Exception');
    }

    /**
     * test isValid() and getErrors()
     */
    public function testIsValid()
    {
        // setup input to test
        $_POST['text'] = 'value';  // first create the POST value, because Input->$posted is set on construction.
        $input = Form::text('text');
        $this->validator->addValidation($input, 'required|min:5|between:4,8', 'This is a message');

        $result = $this->validator->isValid();
        $this->assertTrue($result, 'isValid() should return true when all rules will validate');

        $errors = $this->validator->getErrors();
        $this->assertEmpty($errors, 'errors() should return empty array when all rules validated');

        // now add validations that will fail
        $this->validator->addValidation($input, 'numeric|date');
        $this->assertFalse($this->validator->run(), 'isValid() should return false when not all rules validate');

        $errors = $this->validator->getErrors();
        $this->assertCount(2, $errors['text'], 'errors should contain 2 errors because 2 validations failed');
    }

    /**
     * test that error messages fetch the language file
     */
    public function testMessages()
    {
        // create failing validation
        $input = Form::text('my_name');
        $this->validator->addValidation($input, 'required');
        $this->validator->run();

        $errors = $this->validator->getErrors();
        $this->assertArrayHasKey('my_name', $errors, 'errors array should have input element name as key');
        $error = $errors['my_name'][0];

        $this->assertEquals("The my name field is required.", $error, 'The error message should display from the lang file and use the attribute name');
    }

    /**
     * test that a custom message is returned when given
     */
//    public function testCustomMessage()
//    {
//        // default error message for required is: 'The :attribute field is required'
//
//        $input = Form::text('my_name');
//        $this->validator->addValidation($input, 'required', ':attribute is required.');
//        $this->validator->run();
//
//        $errors = $this->validator->getErrors();
//        $error = $errors['my_name'][0];
//        dd($error);
//        $this->assertEquals("my name is required.", $error, 'The error message should display the custom error message');
//    }

    /**
     * test to make sure that if Input has an array of posted values, are values are checked against the rules
     */
    public function testArrayOfPostedValues()
    {
        // Note that we require 3 different named Input elements, because addValidation() checks if there are
        // already rules defined and in that case doesn't overwrite the $data with another posted value
        // (which is not possible when coding decently)

        // checkboxes require a {$name}-isPosted hidden field to be posted
        $_POST['test1-isPosted'] = 1;
        $_POST['test2-isPosted'] = 1;
        $_POST['test3-isPosted'] = 1;

        // test that should pass
        $_POST['test1'] = array('one', 'two', 'three');
        $input = Form::checkbox('test1')->appendOptions(array('one' => 'one', 'two' => 'two', 'three' => 'three'));
        $this->validator->addValidation($input, 'required|all_in:one,two,three');
        $this->assertTrue($this->validator->run(), 'array of posted values should pass using all_in');

        // single posted value should pass
        $_POST['test2'] = 'two';
        $input = Form::checkbox('test2')->appendOptions(array('one' => 'one', 'two' => 'two', 'three' => 'three'));
        $this->validator->addValidation($input, 'all_in:one,two,three');
        $this->assertTrue($this->validator->run(), 'single posted value should pass using all_in');

        // this should fail
        $_POST['test3'] = array('one', 'two', 'FAIL');
        $input = Form::checkbox('test3')->appendOptions(array('one' => 'one', 'two' => 'two', 'three' => 'three'));
        $this->validator->addValidation($input, 'all_in:one,two,three');
        $this->assertFalse($this->validator->run(), 'array with a not allowed value should not pass using all_in');
    }

    /**
     * run() and isValid() are basically synonyms. They should behave mostly the same, but there are differences...!
     */
    public function testRunAndIsValid()
    {
        // create passing validation
        $_POST['test'] = 'foo';
        $input = Form::text('test');
        $this->validator->addValidation($input, 'alpha');

        $this->assertTrue($this->validator->run());
        $this->assertTrue($this->validator->isValid());

        // now add failing validation
        $this->validator->addValidation($input, 'min:5');
        // isValid will always return the result of the last run
        $this->assertTrue($this->validator->isValid());
        // each time you call run it will create a new WinkValidator and execute all validations
        $this->assertFalse($this->validator->run());
        $this->assertFalse($this->validator->isValid());
    }

}
