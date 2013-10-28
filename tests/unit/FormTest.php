<?php
use Codeception\Util\Stub;
use WinkForm\Form;

/**
 * test the Input class methods
 * @author b-deruiter
 *
 */
class FormTest extends \Codeception\TestCase\Test
{
    /**
     * @var TestForm
     */
    protected $form;

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
        require_once realpath(WINKFORM_PATH.'../../example/TestForm.php');
        $this->form = new TestForm();
    }

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_after()
     */
    protected function _after()
    {
    }

    /**
     * test creation of form with all input elements
     */
    public function testCreation()
    {
        $this->assertTrue($this->form instanceof \WinkForm\Form);
    }

    /**
     * test setting of enctype
     */
    public function testEnctype()
    {
        $form = $this->form;

        $this->assertEquals(Form::ENCTYPE_DEFAULT, $form->getEnctype());

        // upon rendering the form head all objects are checked for file input objects and the enctype is set
        $form->render();

        $this->assertEquals(Form::ENCTYPE_FILE, $form->getEnctype());
    }

    /**
     * test default validation of DateInput and DateRangeInput
     */
    public function testDateValidation()
    {
        // confirm when nothing posted nothing will be invalidated
        $this->assertTrue($this->form->validate());
        $this->assertEmpty($this->form->oDate->getInvalidations());

        // create invalid posted values
        $_POST = array('date' => '123-123', 'dateRange-from' => '1', 'dateRange-to' => '2');
        $form = new TestForm();
        $this->assertFalse($form->validate());
        $this->assertNotEmpty($form->oDate->getInvalidations());
        $this->assertNotEmpty($form->oDateRange->getInvalidations());
    }

}
