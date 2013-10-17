<?php
use Codeception\Util\Stub;
use WinkForm\Input\Dropdown;
use WinkForm\Input\RadioInput;

// tests for issue #14
// Using Dropdown::setSelected() with no flag means the posted value will always be overwritten by the initial value.
// It is necessary to use the flag Input::INPUT_SELECTED_INITIALLY_ONLY, but this is already default behavior for
// other type of fields.
class DropdownTest extends \Codeception\TestCase\Test
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
     * using setSelected with dropdown
     */
    public function testDropdown()
    {
        $dd = new Dropdown('foo');
        $dd->appendOptions(array(1 => 'one', 'two', 'three'));
        $dd->setValue(1);
        $render = $dd->render();
        $this->assertContains('value="1" selected', $render);
        $dd->setSelected(2);
        $render = $dd->render();
        $this->assertContains('value="2" selected', $render);
        $this->assertNotContains('value="1" selected', $render);

        // using setSelected should not overwrite posted value
        $_POST['foo'] = 3;
        $dd = new Dropdown('foo', 1);
        $dd->appendOptions(array(1 => 'one', 'two', 'three'));
        $render = $dd->render();
        $this->assertContains('value="3" selected', $render);
        $dd->setSelected(2);
        $render = $dd->render();
        $this->assertContains('value="3" selected', $render, 'using setSelected should not overwrite posted value');

        // TODO write tests for multiple dropdown
    }

    /**
     * using setSelected with radio options
     */
    public function testRadio()
    {
        $input = new RadioInput('foo', 1);
        $input->appendOptions(array(1 => 'one', 'two', 'three'));
        $input->setSelected(2);
        $render = $input->render();
        dd($render);
    }


    // TODO I think the problem lies with checkbox which can be a single or multiple checkboxes
    // so if values is not empty, value should be treated differently

}
