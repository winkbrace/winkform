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

}
