<?php
use WinkForm\Input\Dropdown;
use Codeception\Util\Stub;

/**
 * test the Input class methods
 * @author b-deruiter
 *
 */
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
     * test setting key '0' as selected
     */
    public function testRenderOptionSelected()
    {
        $input = new Dropdown('test');
        $input->appendOptions(array(1 => 'one', 2 => 'two', 0 => 'zero'));
        $input->setSelected('0');

        $this->assertEquals('0', $input->getSelected());

        $render = $input->render();
        $this->assertContains('value="0" selected="selected"', $render);
    }

}
