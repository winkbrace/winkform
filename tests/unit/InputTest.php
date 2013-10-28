<?php
use Codeception\Util\Stub;
use WinkForm\Form;
use WinkForm\Input\Input;
use WinkForm\Input\TextInput;
use WinkForm\Input\RadioInput;

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
     * test input creation
     */
    public function testCreationWithInitialValue()
    {
        $input = new TextInput('test', 'initial value');
        $this->assertInstanceOf('\WinkForm\Input\TextInput', $input);
        $this->assertEquals('test', $input->getName());
        $this->assertEquals('test', $input->getId());
        $this->assertEquals('initial value', $input->getValue());

        // test rendering id, name and value
        $render = $input->render();
        $this->assertContains('id="test"', $render);
        $this->assertContains('name="test"', $render);
        $this->assertContains('value="initial value"', $render);
    }

    /**
     * test input value
     */
    public function testValue()
    {
        $input = new TextInput('test');
        $this->assertNull($input->renderValue());
        $input->setValue('some value');
        $this->assertEquals(' value="some value"', $input->renderValue());
        // selected will overwrite value (so we can always use setSelected when setting value from database)
        $input->setSelected('other value');
        $this->assertEquals(' value="other value"', $input->renderValue());
    }

    /**
     * test label
     */
    public function testLabel()
    {
        $input = new TextInput('test');
        $input->setLabel('label');
        $this->assertEquals('<label for="test">label</label> ', $input->renderLabel());
    }

    /**
     * passing anything other than array should invalidate the validator and not set the Input array values
     */
    public function testInvalidValuesAndLabels()
    {
        $input = new RadioInput('test');
        $input->setValues('value');
        $this->assertEquals(array(), $input->getValues());
        $input->setLabels('label');
        $this->assertEquals(array(), $input->getLabels());
    }

    /**
     * passing anything other than array should invalidate the validator and not set the Input array values
     * Upon rendering an exception should be thrown
     * @expectedException         Exception
     */
    public function testInvalidInputThrowsExceptionOnRender()
    {
        $input = Form::radio('test')->setValues('invalid')->setLabels('invalid');
        $input->render();
        $this->fail('rendering input element with invalid attributes should throw exception');
    }

    /**
     * the arrays values and labels can be set seperately via setValues and setLabels
     *   (NOT setValue or setLabel, because those set the variables and not the arrays)
     * or together via appendOption, appendOptions or prependOption
     */
    public function testValuesAndLabels()
    {
        $input = new RadioInput('test');
        $input->setValues(array('val1','val2'));
        $this->assertEquals(array('val1','val2'), $input->getValues());

        $input->setLabels(array('label1','label2'));
        $this->assertEquals(array('label1','label2'), $input->getLabels());

        $input->appendOption('val3', 'label3');
        $this->assertEquals(array('val1','val2','val3'), $input->getValues());
        $this->assertEquals(array('label1','label2','label3'), $input->getLabels());

        $input->appendOptions(array(
            'val4' => 'label4',
            'val5' => 'label5',
        ));
        $this->assertEquals(array('val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertEquals(array('label1','label2','label3','label4','label5'), $input->getLabels());

        $input->prependOption('val0', 'label0');
        $this->assertEquals(array('val0','val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertEquals(array('label0','label1','label2','label3','label4','label5'), $input->getLabels());

        $input->prependOptions(array('pre0' => 'prealabel0', 'pre1' => 'prealabel1'));
        $this->assertEquals(array('pre0','pre1','val0','val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertEquals(array('prealabel0','prealabel1','label0','label1','label2','label3','label4','label5'), $input->getLabels());
    }

    /**
     * test class
     */
    public function testClass()
    {
        $input = new TextInput('test');
        $input->setClass('class');
        $this->assertEquals(' class="class"', $input->renderClass());
        $input->addClass('secondClass');
        $this->assertEquals(' class="class secondClass"', $input->renderClass());
        $this->assertEquals(array('class', 'secondClass'), $input->getClasses());
        $input->removeClass('class');
        $this->assertEquals(' class="secondClass"', $input->renderClass());
    }

    /**
     * test id
     */
    public function testId()
    {
        $input = new TextInput('test');
        $this->assertEquals(' id="test"', $input->renderId());
        $input->setId('id');
        $this->assertEquals(' id="id"', $input->renderId());
    }

    /**
     * test attribute disabled
     */
    public function testInputDisabled()
    {
        $input = new TextInput('test');
        $this->assertNull($input->renderDisabled(), 'Initially disabled is null');
        $input->setDisabled('invalid');
        $this->assertNull($input->renderDisabled(), 'Giving an invalid option should not change disabled');

        $input->setDisabled('disabled');
        $this->assertEquals(' disabled="disabled"', $input->renderDisabled());
        $input->setDisabled('readonly');
        $this->assertEquals(' readonly="readonly"', $input->renderDisabled());

        $input->removeDisabled();
        $this->assertNull($input->renderDisabled(), 'removeDisabled should remove the attribute value');
    }

    /**
     * test hidden attribute
     */
    public function testInputHidden()
    {
        $input = new TextInput('test');
        $this->assertFalse($input->getHidden(), 'Initially hidden is false');
        $input->setHidden('invalid');
        $this->assertFalse($input->getHidden(), 'Giving an invalid option should not change hidden');

        $input->setHidden(true);
        $this->assertTrue($input->getHidden());
        $input->setHidden(false);
        $this->assertFalse($input->getHidden());
    }

    /**
     * test $selected attribute
     */
    public function testInputSelected()
    {
        $input = new TextInput('test', 'value');
        $this->assertNull($input->getSelected());
        $input->setSelected('selected');
        $this->assertEquals('selected', $input->getSelected());

        // test overrule post
        $_POST['foo'] = 'one';
        $input = new TextInput('foo');
        $this->assertEquals('one', $input->getSelected());
        $input->setSelected('two', Input::INPUT_OVERRULE_POST);
        $this->assertEquals('two', $input->getSelected());
    }

    /**
     * test style attribute
     */
    public function testInputStyle()
    {
        $input = new TextInput('test');
        $input->setStyle(' text-align:right; ');
        $input->setWidth(300);
        $input->setHidden(true);
        $expected = array(
            'text-align' => 'right',
            'width' => '300px',
            'display' => 'none',
        );
        $this->assertEquals($expected, $input->getStyles()->all());
        $this->assertEquals(' style="text-align:right; width:300px; display:none;"', $input->renderStyle());
        // test removing style with spaces around
        $input->removeStyle(' text-align:right; ');
        $this->assertEquals(' style="width:300px; display:none;"', $input->renderStyle());
        $input->setWidth(200);
        $this->assertEquals(' style="width:200px; display:none;"', $input->renderStyle());

        $input->setHidden(false);
        $this->assertEquals(array('width' => '200px'), $input->getStyles()->all(), 'setting hidden to false should remove the display style');
    }

    /**
     * test passing down style to child Input object
     */
    public function testStyleInheritance()
    {
        $input = new \WinkForm\Input\DateRangeInput('test', date('d-m-Y', strtotime('1 week ago')), date('d-m-Y'));
        $input->setWidth(300);
        $input->setHidden(true);
        $input->addStyle('padding:5px; margin:5px;');
        $expected = array(
            'width' => '300px',
            'display' => 'none',
            'padding' => '5px',
            'margin' => '5px',
        );
        $this->assertEquals($expected, $input->getStyles()->all());

        $from = $input->getDateFrom();
        // only width does not get copied down
        unset($expected['width']);
        $this->assertEquals($expected, $from->getStyles()->all());
    }

    /**
     * test title attribute
     */
    public function testInputTitle()
    {
        $input = new TextInput('test');
        $this->assertNull($input->renderTitle());
        $input->setTitle('title');
        $this->assertEquals(' title="title"', $input->renderTitle());
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

    /**
     * test Observer Pattern implementation
     */
    public function testObserver()
    {
        $weeks = new \WinkForm\Input\WeekRangeInput('weeks', '2013-01', '2013-02');
        $from = $weeks->getWeekFrom();
        $to = $weeks->getWeekTo();

        $weeks->addClass('disco lightning');
        // classes should be passed down to the child WeekInputs
        $this->assertEquals(array('week-dropdown', 'disco', 'lightning'), $from->getClasses());
        $this->assertEquals(array('week-dropdown', 'disco', 'lightning'), $to->getClasses());
        // classes should be also passed down to the 2 Dropdowns in each WeekInput
        $render = $from->render();
        $this->assertContains('class="week-dropdown disco lightning"', $render);

        $weeks->addStyle('border:2px dotted orange')
            ->setWidth(150)
            ->setHidden(true);
        $style = array(
            'border' => '2px dotted orange',
            'width' => '75px',  // width is shared equally between the 2 WeekInputs
            'display' => 'none',
        );
        $this->assertEquals($style, $from->getStyles()->all());
        $this->assertEquals($style, $to->getStyles()->all());

        $weeks->setRequired(true);
        $this->assertEquals(true, $from->getRequired());
        $this->assertEquals(true, $to->getRequired());
    }

    /**
     * test that all Month inputs observe well
     */
    public function testMonthRangeObserver()
    {
        $months = new \WinkForm\Input\MonthRangeInput('months', '2013-01', '2013-02');
        $months->setRequired(true);
        $this->assertEquals(true, $months->getMonthFrom()->getRequired());
        $render = $months->render();
        $this->assertContains('required', $render);
    }

    /**
     * test that the Address inputs observe well
     */
    public function testAddressObserver()
    {
        $input = new \WinkForm\Input\AddressInput('foo');
        $input->setRequired(true);
        $this->assertEquals(true, $input->getHouseNumber()->getRequired());
        $render = $input->render();
        $this->assertContains('required', $render);
    }

    /**
     * test that the Address inputs observe well
     */
    public function testDateObserver()
    {
        $input = new \WinkForm\Input\DateRangeInput('foo');
        $input->setRequired(true);
        $this->assertEquals(true, $input->getDateTo()->getRequired());
        $render = $input->render();
        $this->assertContains('required', $render);
    }

    /**
     * test rendering of the label
     */
    public function testRenderWithLabel()
    {
        $input = new TextInput('foo', 'val');

        $render = $input->render();
        $this->assertNotContains('<label', $render, 'if no label is set, none should be rendered');

        $input->setLabel('some label');
        $render = $input->render();
        $this->assertContains('<label for="foo"', $render, 'by default Inputs are rendered with a label if they have one');

        $input->setRenderWithLabel(false);
        $render = $input->render();
        $this->assertNotContains('<label for="foo"', $render, 'if renderWithLabel is set to false, no label should be rendered.');

        $input->setRenderWithLabel('invalid');
        $this->assertFalse($input->getRenderWithLabel(), 'An invalid argument should not change the attribute');
    }

}
