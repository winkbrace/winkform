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
     * @expectedException         InvalidArgumentException
     */
    public function testInvalidDateFormat()
    {
        $input = new DateInput('test');
        $input->setDateFormat('invalid');
        $this->fail('setDateFormat() with an invalid date should throw InvalidArgumentException');
    }

    /**
     * test getCorrectedPostedDate()
     */
    public function testCorrectPostedDate()
    {
        $_POST['test'] = '1-8-2013';
        $input = new DateInput('test');
        $this->assertEquals('01-08-2013', $input->getPosted());

        $input->setDateFormat('j-n-Y');
        $this->assertEquals('1-8-2013', $input->getPosted());
    }

    /**
     * test that setValue checks date
     */
    public function testSetValue()
    {
        $input = new DateInput('test', date('d-m-Y'));
        $this->assertEquals(date('d-m-Y'), $input->getValue());

        // should not be set
        $input->setValue('wrong');
        $this->assertEquals(date('d-m-Y'), $input->getValue());
    }

    /**
     * test setting jquery date picker date format based on php date format
     */
    public function testJsDateFormat()
    {
        $input = new DateInput('test');
        $input->setDateFormat('m.d.y');
        $html = $input->render();
        $this->assertContains('"dateFormat":"mm.dd.y"', $html, 'setting php date format should also adjust js date format');
    }
    
    /**
     * DateInput should properly render the label
     */
    public function testRenderLabel()
    {
        $input = new DateInput('test');
        $input->setLabel('a date label');
        $render = $input->render();
        $this->assertContains('<label for="test">a date label</label>', $render);
    }
    
}
