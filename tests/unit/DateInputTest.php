<?php
use WinkForm\Input\DateInput;
use Codeception\Util\Stub;

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
     * test changing date format
     */
    public function testDateFormat()
    {
        $input = new DateInput('test');
        // default setting
        // we can check that everything was set via the validation
        $this->assertEquals(array('date_format:d-m-Y'), $input->getValidations());
        
        $input->setDateFormat('Ymd');
        $this->assertEquals(array('date_format:Ymd'), $input->getValidations());
    }
    
    /**
     * test getCorrectedPostedDate()
     */
    public function testCorrectPostedDate()
    {
        $_POST['test'] = '1-8-2013';
        $input = new DateInput('test');
        $this->assertEquals('01-08-2013', $input->getPosted());
        
        $input->setDateFormat('j-n-2013');
        $this->assertEquals('1-8-2013', $input->getPosted());
    }

}
