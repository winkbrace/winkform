<?php
use WinkForm\Input\DateInput;

use Codeception\Util\Stub;
use WinkForm\Input\TextInput;

/**
 * test the Input class methods
 * @author b-deruiter
 *
 */
class DateInputTest extends \Codeception\TestCase\Test
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
    public function testDateFormat()
    {
        $_POST['test'] = '20130925';
        $input = new DateInput('test');
        $input->setDateFormat('Ymd');
        $this->assertTrue(true);
    }

}
