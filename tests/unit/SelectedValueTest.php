<?php

use Codeception\Util\Stub;
use WinkForm\Input\Dropdown;
use WinkForm\Input\RadioInput;
use WinkForm\Input\DateInput;
use WinkForm\Input\Checkbox;

// tests for issue #14
// Using Dropdown::setSelected() with no flag means the posted value will always be overwritten by the initial value.
// It is necessary to use the flag Input::INPUT_SELECTED_INITIALLY_ONLY, but this is already default behavior for
// other type of fields.

/**
 * Input has 3 variables to hold a value to display or select:
 * 1. $this->posted      this is the value that has been posted
 * 2. $this->selected    this is the value that can always be set using setSelected.
 * 3. $this->value       this is the value that is INITIALLY set
 * which of these values is found first (in this order) determines the value to display or select
 */

/**
 * SelectedValueTest
 * @author b-deruiter
 */
class SelectedValueTest extends \Codeception\TestCase\Test
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
        $render = $input->render();
        $this->assertContains('value="1" checked', $render);
        
        $input->setSelected(2);
        $render = $input->render();
        $this->assertContains('value="2" checked', $render);
        
        // using setSelected should not overwrite posted value
        $_POST['foo'] = 3;
        $input = new RadioInput('foo', 1);
        $input->appendOptions(array(1 => 'one', 'two', 'three'));
        $render = $input->render();
        $this->assertContains('value="3" checked', $render);
        $input->setSelected(2);
        $render = $input->render();
        $this->assertContains('value="3" checked', $render, 'using setSelected should not overwrite posted value');
    }

    /**
     * test multiple checkbox
     */
    public function testMultpileCheckbox()
    {
        $input = new Checkbox('foo', 1);
        $input->appendOptions(array(1 => 'one', 'two', 'three'));
        $render = $input->render();
        $this->assertContains('value="1" checked', $render);
        
        $input->setSelected(2);
        $render = $input->render();
        $this->assertContains('value="2" checked', $render);
        
        // using setSelected should not overwrite posted value
        $_POST['foo'] = array(3);
        $_POST['foo-isPosted'] = 1;
        $input = new Checkbox('foo', 1);
        $input->appendOptions(array(1 => 'one', 'two', 'three'));
        $render = $input->render();
        $this->assertContains('value="3" checked', $render);
        $input->setSelected(2);
        $render = $input->render();
        $this->assertContains('value="3" checked', $render, 'using setSelected should not overwrite posted value');
    }
    
    /**
     * test single checkbox
     */
    public function testSingleCheckbox()
    {
        $input = new Checkbox('foo', 1);
        $render = $input->render();
        $this->assertNotContains('checked', $render);
        
        $input->setSelected(1);
        $render = $input->render();
        $this->assertContains('checked', $render);
        
        $input->setSelected(2);
        $render = $input->render();
        $this->assertNotContains('checked', $render, 'setSelected with invalid value should remove the checked attribute');
        
        // using setSelected should not overwrite posted value
        $_POST['foos'] = 1;
        $_POST['foos-isPosted'] = 1;
        $input = new Checkbox('foos', 1);
        $render = $input->render();
        $this->assertContains('checked', $render);
        $input->setSelected(2);
        $render = $input->render();
        $this->assertContains('checked', $render, 'using setSelected with invalid value should not overwrite posted value');
    }
    
    /**
     * test date input
     * test value, selected and posted are properly rendering
     */
    public function testDate()
    {
        // value should be rendered
        $input = new DateInput('test', '10-10-2010');
        $render = $input->render();
        $this->assertContains('value="10-10-2010"', $render);
        
        // selected value should be rendered
        $input->setSelected('11-11-2011');
        $render = $input->render();
        $this->assertContains('value="11-11-2011"', $render);
        
        // posted value should be rendered
        $_POST['test'] = '12-12-2012';
        $input = new DateInput('test');
        $render = $input->render();
        $this->assertContains('value="12-12-2012"', $render);
        
        // selected should not overwrite posted
        $input->setSelected('11-11-2011');
        $render = $input->render();
        $this->assertContains('value="12-12-2012"', $render);
    }
    
}
