<?php
use WinkForm\Input\DateRangeInput;
use Codeception\Util\Stub;

/**
 * test the Input class methods
 * @author b-deruiter
 *
 */
class DaterRangeInputTest extends \Codeception\TestCase\Test
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
     * test changing date format
     */
    public function testSetSelected()
    {
        // init
        $input = new DateRangeInput('test', '01-02-2000', '02-02-2000');
        $this->assertEquals(null, $input->getDateFrom()->getSelected());
        $this->assertEquals(null, $input->getDateTo()->getSelected());

        // set selected
        $input->setSelected(array('12-12-2012', '01-01-2013'));
        $this->assertEquals('12-12-2012', $input->getDateFrom()->getSelected());
        $this->assertEquals('01-01-2013', $input->getDateTo()->getSelected());

        // test wrong input
        $input->setSelected(array('wrong'));
        $this->assertEquals('12-12-2012', $input->getDateFrom()->getSelected());
        $this->assertEquals('01-01-2013', $input->getDateTo()->getSelected());
    }

}
