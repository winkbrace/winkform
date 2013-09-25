<?php
use Codeception\Util\Stub;
use WinkForm\Input\TextInput;

/**
 * test the Input class methods
 * @author b-deruiter
 *
 */
class InputTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_before()
     */
    protected function _before()
    {
    }

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_after()
     */
    protected function _after()
    {
    }

    /**
     * test renderValidationErrors()
     */
    public function testRenderValidationErrors()
    {
        $input = new TextInput('foo', 'value');
        $input->setValue(array('one', 'two')); // setValue doesn't accept array
        $input->addClass('$@#$%^&'); // no special chars allowed in class name
        $errors = $input->renderValidationErrors('my custom message');
        $this->assertContains('div class="error"', $errors);
        $this->assertContains('my custom message', $errors);

        $input = new TextInput('foo', 'value');
        $errors = $input->renderValidationErrors();
        $this->assertEmpty($errors);
    }
    
    /**
     * test addValidation
     */
    public function testAddValidation()
    {
        $input = new TextInput('foo', 'value');
        $input->addValidation('required|min:5');
        $expected = array('required', 'min:5');
        $this->assertEquals($expected, $input->getValidations());
    }
    
    /**
     * test remove validation
     */
    public function testRemoveValidation()
    {
        $input = new TextInput('foo', 'value');
        $input->addValidation('required|min:5|max:25|not_in:14,15,16');
        // should remove based on the raw rule name
        $input->removeValidation('min|max:25');
        $expected = array('required', 'not_in:14,15,16');
        $this->assertEquals($expected, $input->getValidations());
    }
    
    /**
     * test replace validation
     */
    public function testReplaceValidation()
    {
        $input = new TextInput('foo', 'value');
        $input->addValidation('required|min:5|max:25|not_in:14,15,16');
        // should replace based on the raw rule name
        $input->replaceValidation('min:19|max:30');
        $expected = array('required', 'not_in:14,15,16', 'min:19', 'max:30'); // note: replacements are placed at end
        $this->assertEquals($expected, $input->getValidations());
    }

}
