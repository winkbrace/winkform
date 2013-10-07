<?php namespace WinkForm\Input;

use WinkForm\Button\SubmitButton;
use WinkForm\Button\Button;
use WinkForm\Button\ImageButton;

if (strpos(get_include_path(), 'private') === false)
    set_include_path('/web/www/private'.PATH_SEPARATOR.get_include_path());

require_once 'simpletest/autorun.php';
require_once 'WinkForm/require.php';
require_once 'Query.php';

/**
 * TestsInput (always name Tests and not Test, to not confuse a method for a constructor)
 *
 * Class to test the form Input classes using SimpleTest
 * @author Bas de Ruiter
 *
 */
class TestsInput extends \UnitTestCase
{
    /**
     *
     */
    public function testInputName()
    {
        $input = new TextInput('test');
        $this->assertEqual(' name="test"', $input->renderName());
    }
    
    /**
     *
     */
    public function testInputValue()
    {
        $input = new TextInput('test');
        $this->assertNull($input->renderValue());
        $input->setValue('value');
        $this->assertEqual(' value="value"', $input->renderValue());
        // selected will overwrite value (so we can always use setSelected when setting value from database)
        $input->setSelected('otherValue');
        $this->assertEqual(' value="otherValue"', $input->renderValue());
    }
    
    /**
     *
     */
    public function testInputLabel()
    {
        $input = new TextInput('test');
        $input->setLabel('label');
        $this->assertEqual('<label for="test">label</label> ', $input->renderLabel());
    }

    /**
     * passing anything other than array should invalidate the validator and not set the Input array values
     */
    public function testInputValuesAndLabelsNoArray()
    {
        $input = new RadioInput('test');
        $input->setValues('value');
        $this->assertIdentical(array(), $input->getValues());
        $input->setLabels('label');
        $this->assertIdentical(array(), $input->getLabels());
        // the class $validate is protected and only with render() is the validity checked and returned if invalid
        $render = $input->render();
        $this->assertEqual("<p>Error rendering RadioInput object with name test</p>\nvalue Given value has to be an array.<br/>\nlabel Given value has to be an array.<br/>\n", $render);
    }
    
    /**
     * the arrays values and labels can be set seperately via setValues and setLabels
     *   (NOT setValue or setLabel, because those set the variables and not the arrays)
     * or together via appendOption, appendOptions or prependOption
     */
    public function testInputValuesAndLabels()
    {
        $input = new RadioInput('test');
        $input->setValues(array('val1','val2'));
        $this->assertIdentical(array('val1','val2'), $input->getValues());
        
        $input->setLabels(array('label1','label2'));
        $this->assertIdentical(array('label1','label2'), $input->getLabels());
        
        $input->appendOption('val3', 'label3');
        $this->assertIdentical(array('val1','val2','val3'), $input->getValues());
        $this->assertIdentical(array('label1','label2','label3'), $input->getLabels());
        
        $input->appendOptions(array(
                        'val4' => 'label4',
                        'val5' => 'label5',
                        ));
        $this->assertIdentical(array('val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertIdentical(array('label1','label2','label3','label4','label5'), $input->getLabels());
        
        $input->prependOption('val0', 'label0');
        $this->assertIdentical(array('val0','val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertIdentical(array('label0','label1','label2','label3','label4','label5'), $input->getLabels());
        
        $input->prependOptions(array('pre0' => 'prealabel0', 'pre1' => 'prealabel1'));
        $this->assertIdentical(array('pre0','pre1','val0','val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertIdentical(array('prealabel0','prealabel1','label0','label1','label2','label3','label4','label5'), $input->getLabels());
        
        $query = new \Query("select 'val6' value, 'label6' label from dual");
        $input->appendOptions($query->fetchArray());
        $this->assertIdentical(array('pre0','pre1','val0','val1','val2','val3','val4','val5','val6'), $input->getValues());
        $this->assertIdentical(array('prealabel0','prealabel1','label0','label1','label2','label3','label4','label5','label6'), $input->getLabels());
    }
    
    /**
     *
     */
    public function testInputClass()
    {
        $input = new TextInput('test');
        $input->setClass('class');
        $this->assertEqual(' class="class"', $input->renderClass());
        $input->addClass('secondClass');
        $this->assertEqual(' class="class secondClass"', $input->renderClass());
        $this->assertEqual(array('class', 'secondClass'), $input->getClasses());
        $input->removeClass('class');
        $this->assertEqual(' class="secondClass"', $input->renderClass());
    }
    
    /**
     *
     */
    public function testInputId()
    {
        $input = new TextInput('test');
        $this->assertEqual(' id="test"', $input->renderId());
        $input->setId('id');
        $this->assertEqual(' id="id"', $input->renderId());
    }
    
    /**
     *
     */
    public function testInputDisabled()
    {
        $input = new TextInput('test');
        $this->assertNull($input->renderDisabled());
        $input->setDisabled('invalid');
        $this->assertNull($input->renderDisabled());
        
        $input->setDisabled('disabled');
        $this->assertEqual(' disabled="disabled"', $input->renderDisabled());
        $input->setDisabled('readonly');
        $this->assertEqual(' readonly="readonly"', $input->renderDisabled());
    }
    
    /**
     *
     */
    public function testInputHidden()
    {
        $input = new TextInput('test');
        $this->assertNull($input->getHidden());
        $input->setHidden('invalid');
        $this->assertNull($input->getHidden());
        
        $input->setHidden(true);
        $this->assertTrue($input->getHidden());
    }
    
    /**
     *
     */
    public function testInputInReportForm()
    {
        $input = new TextInput('test');
        $this->assertFalse($input->getInReportForm());
        $input->setHidden('invalid');
        $this->assertFalse($input->getInReportForm());
        
        $input->setInReportForm(true);
        $this->assertTrue($input->getInReportForm());
    }
    
    /**
     * test $selected
     */
    public function testInputSelected()
    {
        $input = new TextInput('test', 'value');
        $this->assertNull($input->getSelected());
        $input->setSelected('selected');
        $this->assertEqual('selected', $input->getSelected());
        $input->setSelected('other', Input::INPUT_OVERRULE_POST);
    }
    
    /**
     *
     */
    public function testInputStyle()
    {
        $input = new TextInput('test');
        $input->setStyle(' text-align:right; ');
        $input->setWidth(300);
        $input->setHidden(true);
        $this->assertEqual(array('text-align:right','width:300px','display:none'), $input->getStyles());
        $this->assertEqual(' style="text-align:right; width:300px; display:none;"', $input->renderStyle());
        $input->removeStyle(' text-align:right; ');
        $this->assertEqual(' style="width:300px; display:none;"', $input->renderStyle());
        $input->setWidth(200);
        $this->assertEqual(' style="display:none; width:200px;"', $input->renderStyle());
        $input->setHidden(false);
        $this->assertEqual(array('width:200px'), $input->getStyles());
    }
    
    /**
     *
     */
    public function testInputTitle()
    {
        $input = new TextInput('test');
        $this->assertNull($input->renderTitle());
        $input->setTitle('title');
        $this->assertEqual(' title="title"', $input->renderTitle());
    }
    
    /**
     *
     */
    public function testInputWidth()
    {
        $input = new TextInput('test');
        $this->assertNull($input->getWidth());
        $input->setWidth(234);
        $this->assertEqual(234, $input->getWidth());
        $this->assertEqual(' style="width:234px;"', $input->renderStyle());
    }
    

    /**
     * test specific TextInput methods, attributes and render
     */
    public function testButton()
    {
        $input = new Button('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('selected')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200);
        $this->assertEqual('<label for="test">label</label> <input type="button" id="test" class="class" name="test" value="selected" style="display:none; color:red; width:200px;" disabled="disabled" title="title" onchange="alert(\'On change\')" onclick="alert(\'On click\')" />'."\n", $input->render());
    }

    /**
     * test specific Checkbox methods, attributes and render
     */
    public function testCheckbox()
    {
        $input = new Checkbox('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('2')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200)
                ->appendOptions(array(1 => 'one', 2 => 'two', 3 => 'three'));
        $expected = '<label for="test">label</label> <div id="test-container" style="display:none; color:red; width:200px;">'."\n"
                  . '<input type="checkbox" id="test-1" class="class" name="test[]" value="1" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-1">one</label>'."\n"
                  . '<input type="checkbox" id="test-2" class="class" name="test[]" value="2" checked="checked" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-2">two</label>'."\n"
                  . '<input type="checkbox" id="test-3" class="class" name="test[]" value="3" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-3">three</label>'."\n"
                  . '</div>'."\n"
                  . '<input type="hidden" id="test-isPosted" name="test-isPosted" value="1" />'."\n";
        $this->assertEqual($expected, $input->render());
        
        $this->assertNull($input->getRenderInColumns());
        $this->assertEqual('HORIZONTAL', $input->getOrientation());
        
        $input->setOrientation('VERTICAL');
        $this->assertEqual(1, $input->getRenderInColumns());
        
        $input->setOrientation('HORIZONTAL');
        $this->assertEqual(0, $input->getRenderInColumns());
        
        $input->setRenderInColumns(2);
        $this->assertEqual(2, $input->getRenderInColumns());
        
        $expected = '<label for="test">label</label> <div id="test-container" style="display:none; color:red; width:200px;">'."\n"
                  . '<table><tr><td valign="top"><input type="checkbox" id="test-1" class="class" name="test[]" value="1" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-1">one</label>'."\n"
                  . '<br/>'."\n"
                  . '<input type="checkbox" id="test-2" class="class" name="test[]" value="2" checked="checked" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-2">two</label>'."\n"
                  . '<br/>'."\n"
                  . '</td><td valign="top"><input type="checkbox" id="test-3" class="class" name="test[]" value="3" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-3">three</label>'."\n"
                  . '<br/>'."\n"
                  . '</td></tr></table>'."\n"
                  . '</div>'."\n"
                  . '<input type="hidden" id="test-isPosted" name="test-isPosted" value="1" />'."\n";
        $this->assertEqual($expected, $input->render());
        
        // single checkbox
        $input->setValues(array());
        $input->setLabels(array());
        $expected = '<div id="test-container" style="display:none; color:red; width:200px;">'."\n"
                  . '<input type="checkbox" id="test" class="class" name="test" value="2" style="display:none; color:red; width:200px;" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test">label</label>'."\n"
                  . '</div>'."\n"
                  . '<input type="hidden" id="test-isPosted" name="test-isPosted" value="1" />'."\n";
        $this->assertEqual($expected, $input->render());
        
        // single checkbox set selected
        $input = new Checkbox('name', 'Y');
        $input->setLabel('Label')->setSelected('Y');
        $this->assertTrue(strpos($input->render(), 'checked="checked"') !== false);
    }
    
    /**
     * test specific DateInput methods, attributes and render
     */
    public function testDateInput()
    {
        $input = new DateInput('test_date');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('selected')
                ->addStyle('color:red')
                ->setTitle('title');
        $expected = '<span id="test_date-container" style="display:none;"><label for="test_date">label</label> <input type="text" id="test_date" class="class" name="test_date" value="selected" style="color:red; width:80px;" disabled="disabled" maxlength="10" title="title" onchange="alert(\'On change\')" onclick="alert(\'On click\')" />'."\n"
                  . '<img class="date-button" id="button-test_date" src="http://marketingweb.itservices.lan/images/calendar.gif" height="28" style="vertical-align:-85%;" onmouseover="this.style.cursor=\'pointer\';" onmouseout="this.style.cursor=\'auto\';" /></span>'."\n";
        $this->assertEqual($expected, $input->render());
        
        // test that setPosted converts 1-1-2013 to 01-01-2013
        $_POST[$input->getName()] = '1-1-2013';
        \SimpleTestUtil::executeMethod($input, 'setPosted');
        $this->assertEqual($input->getPosted(), '01-01-2013');
    }
    
    /**
     * test specific DateRange methods, attributes and render
     */
    public function testDateRange()
    {
        $input = new DateRangeInput('test', '01-01-2012', '31-01-2012');
        $input->setLabels(array('van', 'tot'));
        $this->assertEqual(' value="01-01-2012"', $input->getDateFrom()->renderValue());
        $this->assertEqual(' value="31-01-2012"', $input->getDateTo()->renderValue());
        $this->assertEqual(array('van', 'tot'), $input->getLabels());
        
        $input->setClass('class')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('selected')
                ->addStyle('color:red')
                ->setTitle('title');
        
        $expected = '<span id="test-from-container" style="display:none;"><label for="test-from">van</label> <input type="text" id="test-from" class="class" name="test-from" value="01-01-2012" style="color:red; width:80px;" maxlength="10" title="title" onchange="alert(\'On change\')" onclick="alert(\'On click\')" />'."\n"
                  . '<img class="date-button" id="button-test-from" src="http://marketingweb.itservices.lan/images/calendar.gif" height="28" style="vertical-align:-85%;" onmouseover="this.style.cursor=\'pointer\';" onmouseout="this.style.cursor=\'auto\';" /></span>'."\n"
                  . '<script type="text/javascript">'."\n"
                  . '                            Calendar.setup({'."\n"
                  . '                                inputField  : "test-from",   // ID of the input field'."\n"
                  . '                                ifFormat    : "%d-%m-%Y",    // the date format'."\n"
                  . '                                button      : "button-test-from",     // ID of the button'."\n"
                  . '                                firstDay    : 1'."\n"
                  . '                            });'."\n"
                  . '                            </script>'."\n"
                  . '<span id="test-to-container" style="display:none;"><label for="test-to">tot</label> <input type="text" id="test-to" class="class" name="test-to" value="31-01-2012" style="color:red; width:80px;" maxlength="10" title="title" onchange="alert(\'On change\')" onclick="alert(\'On click\')" />'."\n"
                  . '<img class="date-button" id="button-test-to" src="http://marketingweb.itservices.lan/images/calendar.gif" height="28" style="vertical-align:-85%;" onmouseover="this.style.cursor=\'pointer\';" onmouseout="this.style.cursor=\'auto\';" /></span>'."\n"
                  . '<script type="text/javascript">'."\n"
                  . '                            Calendar.setup({'."\n"
                  . '                                inputField  : "test-to",   // ID of the input field'."\n"
                  . '                                ifFormat    : "%d-%m-%Y",    // the date format'."\n"
                  . '                                button      : "button-test-to",     // ID of the button'."\n"
                  . '                                firstDay    : 1'."\n"
                  . '                            });'."\n"
                  . '                            </script>'."\n";
        $this->assertEqual($expected, str_replace("\r\n", "\n", $input->render()));
    }
    
    /**
     * test specific Dropdown methods, attributes and render
     */
    public function testDropdown()
    {
        $input = new Dropdown('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('2')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200)
                ->setMultiple(true)
                ->setSize(5)
                ->appendOption(1, 'one', 'first')
                ->appendOption(2, 'two', 'first')
                ->appendOption(3, 'three', 'second')
                ->appendOption(4, 'four', 'second')
                ->prependOption('', '--select--', 'zero');
        
        $expected = '<label for="test">label</label> <span class="dropdown-multipletext">Hold Ctrl to (de)select multiple.</span><br/>'."\n"
                  . '<select id="test" class="class" name="test[]" multiple="multiple" size="5" style="display:none; color:red; width:200px;" disabled="disabled" title="title" onchange="alert(\'On change\')">'."\n"
                  . '<optgroup label="zero">'."\n"
                  . '<option id="test-" value="">--select--</option>'."\n"
                  . '</optgroup>'."\n"
                  . '<optgroup label="first">'."\n"
                  . '<option id="test-1" value="1">one</option>'."\n"
                  . '<option id="test-2" value="2" selected="selected">two</option>'."\n"
                  . '</optgroup>'."\n"
                  . '<optgroup label="second">'."\n"
                  . '<option id="test-3" value="3">three</option>'."\n"
                  . '<option id="test-4" value="4">four</option>'."\n"
                  . '</optgroup>'."\n"
                  . '</select>'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific FileInput methods, attributes and render
     */
    public function testFileInput()
    {
        $input = new FileInput('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('2')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200);
        
        $expected = '<label for="test">label</label> <input type="file" id="test" class="class" name="test" style="display:none; color:red; width:200px;" disabled="disabled" title="title" onchange="alert(\'On change\')" onclick="alert(\'On click\')" />'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific HiddenInput methods, attributes and render
     */
    public function testHiddenInput()
    {
        $input = new HiddenInput('test', 'value');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('2')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200);
        
        $expected = '<input type="hidden" id="test" class="class" name="test" value="2" style="display:none; color:red; width:200px;" disabled="disabled" title="title" onchange="alert(\'On change\')" />'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific ImageButton methods, attributes and render
     */
    public function testImageButton()
    {
        $input = new ImageButton('test');
        $input->setClass('class')
                ->setHidden(true)
                ->setLabel('label')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200)
                ->setSrc('http://localhost/mw/campaign/img/submit.png');
        
        $expected = '<label for="test">label</label> <input type="image" src="http://localhost/mw/campaign/img/submit.png" alt="" id="test" class="class" name="test" style="display:none; color:red; width:200px;" title="title" onclick="alert(\'On click\')" />'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific RadioInput methods, attributes and render
     */
    public function testRadioInput()
    {
        $input = new RadioInput('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('2')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200)
                ->appendOptions(array(1 => 'one', 2 => 'two', 3 => 'three'));
        
        $expected = '<label for="test">label</label> <div id="test-container" style="display:none; color:red; width:200px;">'."\n"
                  . '<input type="radio" id="test-1" class="class" name="test" value="1" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-1">one</label>'."\n"
                  . '<input type="radio" id="test-2" class="class" name="test" value="2" checked="checked" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-2">two</label>'."\n"
                  . '<input type="radio" id="test-3" class="class" name="test" value="3" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-3">three</label>'."\n"
                  . '</div>'."\n";
        
        $this->assertEqual($expected, $input->render());
        
        $input->setRenderInColumns(2);
        $this->assertEqual(2, $input->getRenderInColumns());
        $input->setLineEnd('<span class="verplicht">*</span>');
        $this->assertEqual('<span class="verplicht">*</span>', $input->getLineEnd());
        
        $expected = '<label for="test">label</label> <div id="test-container" style="display:none; color:red; width:200px;">'."\n"
                  . '<table><tr><td valign="top"><input type="radio" id="test-1" class="class" name="test" value="1" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-1">one</label>'."\n"
                  . '<br/>'."\n"
                  . '<input type="radio" id="test-2" class="class" name="test" value="2" checked="checked" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-2">two</label>'."\n"
                  . '<br/>'."\n"
                  . '</td><td valign="top"><input type="radio" id="test-3" class="class" name="test" value="3" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n"
                  . '<label for="test-3">three</label>'."\n"
                  . '<br/>'."\n"
                  . '</td></tr></table>'."\n"
                  . '</div>'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific SubmitButton methods, attributes and render
     */
    public function testSubmitButton()
    {
        $input = new SubmitButton('test', 'click me');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setLabel('label')
                ->setSelected('2')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200);
        $expected = '<label for="test">label</label> <input type="submit" id="test" class="class" name="test" value="2" style="color:red; width:200px;" disabled="disabled" title="title" onclick="alert(\'On click\')" />'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific TextArea methods, attributes and render
     */
    public function testTextAreaInput()
    {
        $input = new TextAreaInput('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('selected')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200)
                ->setWrapStyle('pre')
                ->setRows(10)
                ->setCols(20);
        $expected = '<label for="test">label</label> <textarea id="test" class="class" name="test" rows="10" cols="20" style="display:none; color:red; width:200px; white-space:pre;" disabled="disabled" title="title" onchange="alert(\'On change\')" onclick="alert(\'On click\')">'."\n"
                  . 'selected</textarea>'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific TextInput methods, attributes and render
     */
    public function testTextInput()
    {
        $input = new TextInput('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setMaxLength(10)
                ->setSelected('selected')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200);
        $expected = '<label for="test">label</label> <input type="text" id="test" class="class" name="test" value="selected" style="display:none; color:red; width:200px;" disabled="disabled" maxlength="10" title="title" onchange="alert(\'On change\')" onclick="alert(\'On click\')" />'."\n";
        $this->assertEqual($expected, $input->render());
    }
    
    /**
     * test specific WeekInput methods, attributes and render
     */
    public function testWeekInput()
    {
        $input = new WeekInput('test');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->setSelected('2011-39')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200);
        
        // test that it renders
        $input->render();
    }
    
    /**
     * test specific WeekRange methods, attributes and render
     */
    public function testWeekRange()
    {
        $input = new WeekRangeInput('test', '2010-01', '2011-01');
        $input->setClass('class')
                ->setDisabled('disabled')
                ->setHidden(true)
                ->setLabel('label')
                ->addStyle('color:red')
                ->setTitle('title')
                ->setWidth(200);
        
        // test that it renders
        $input->render();
    }
    
    /**
     * test MonthInput
     */
    public function testMonth()
    {
        $input = new MonthInput('foo', '2012-04');
        $input->setLabel('Foo');
        $this->assertEqual($input->getSelected(), '2012-04');
        $input->setSelected('2013-05');
        $this->assertEqual($input->getSelected(), '2013-05');
        
        // test that it renders
        $result = $input->render();
        $this->assertTrue(strpos($result, '<script') !== false);
        $this->assertTrue(strpos($result, 'name="foo_year"') !== false);
        $this->assertTrue(strpos($result, 'name="foo_month"') !== false);
        $this->assertTrue(strpos($result, 'name="foo"') !== false);
    }
    
    /**
     * test AddressInput
     */
    public function testAddress()
    {
        $input = new AddressInput('foo');
        $input->setLabel('Foo');
        $this->assertIsA($input->getPostcode(), 'TextInput');
        $this->assertIsA($input->getHouseNumber(), 'TextInput');
        $this->assertIsA($input->getHouseNumberExtension(), 'TextInput');
        
        $result = $input->render();
        $this->assertTrue(strpos($result, '<script') !== false);
        $this->assertTrue(strpos($result, 'name="postcode"') !== false);
        $this->assertTrue(strpos($result, 'name="huisnr"') !== false);
        $this->assertTrue(strpos($result, 'name="toevoeging"') !== false);
    }
    
    /**
     * Test the handling of posting the form fields
     */
    public function testPostAll()
    {
        // don't create all the formfields when the tests are run from command line
        if (\TextReporter::inCli())
            return;
            
        $checkbox = new Checkbox('checkbox');
        $checkbox->appendOptions(array(
                    'cb1' => 'checkbox1',
                    'cb2' => 'checkbox2',
                    'cb3' => 'checkbox3',
                    ));
        $checkbox->setSelected(array('cb1','cb3'));
        
        $dateInput = new DateInput('dateInput', '10-03-1979');
        
        $dateRange = new DateRangeInput('dateRange', '01-01-2012', '31-01-2012');
        
        $dropdown = new Dropdown('dropdown');
        $dropdown->appendOptions(array(
                    'dd1' => 'dropdown1',
                    'dd2' => 'dropdown2',
                    'dd3' => 'dropdown3',
                    ));
        $dropdown->setSelected('dd2');
        
        $dd2 = new Dropdown('dd2');
        $dd2->setMultiple(true);
        $dd2->appendOptions(array(
                    'dd1' => 'dropdown1',
                    'dd2' => 'dropdown2',
                    'dd3' => 'dropdown3',
                    ));
        $dd2->setSelected(array('dd2','dd3'));
        
        $fileInput = new FileInput('fileInput');
        
        $hiddenInput = new HiddenInput('hiddenInput', 'secret');
        
        $imageButton = new ImageButton('imageButton');
        $imageButton->setSrc('http://localhost/mw/campaign/img/submit.png');
        
        $radioInput = new RadioInput('radioInput');
        $radioInput->appendOptions(array(
                    'radio1' => 'radio1',
                    'radio2' => 'radio2',
                    ));
        $radioInput->setSelected('radio1');
        
        $submitButton = new SubmitButton('submitButton', 'Submit me');
        
        $textAreaInput = new TextAreaInput('textAreaInput', 'Textarea content');
        
        $textInput = new TextInput('textInput', 'text');
        
        $weekInput = new WeekInput('weekInput', '2012-10');
        
        $weekRange = new WeekRangeInput('weekRange', '2012-01', '2012-20');
        
        echo '<div style="border: 2px solid blue; padding:20px; margin:20px; font-family:Arial;">'."\n";
        echo '<p style="font-weight:bold;">This is a form with all input objects to test the handling of posting. Press the submit button or image to post.</p>'."\n";
        echo '<form method="post" action="" enctype="multipart/form-data">'."\n";
        echo $checkbox->render()."<br/>";
        echo $dateInput->render()."<br/>";
        echo $dateRange->render()."<br/>";
        echo $dropdown->render()."<br/>";
        echo $dd2->render()."<br/>";
        echo $fileInput->render()."<br/>";
        echo $hiddenInput->render()."<br/>";
        echo $radioInput->render()."<br/>";
        echo $textAreaInput->render()."<br/>";
        echo $textInput->render()."<br/>";
        echo $weekInput->render()."<br/>";
        echo $weekRange->render()."<br/>";
        echo $submitButton->render()."<br/>";
        echo $imageButton->render()."<br/>";
        echo "</form>\n";
        echo "</div>\n";
        
        if ($submitButton->isPosted() || $imageButton->isPosted())
        {
            $this->assertEqual(array('cb1','cb3'), $_POST['checkbox']);
            $this->assertEqual('1', $_POST['checkbox-isPosted']);
            $this->assertEqual('10-03-1979', $_POST['dateInput']);
            $this->assertEqual('01-01-2012', $_POST['dateRange-from']);
            $this->assertEqual('31-01-2012', $_POST['dateRange-to']);
            $this->assertEqual('dd2', $_POST['dropdown']);
            $this->assertEqual(array('dd2','dd3'), $_POST['dd2']);
            $this->assertEqual($fileInput->getPosted(), $_FILES['fileInput']['tmp_name']);
            $this->assertEqual('secret', $_POST['hiddenInput']);
            $this->assertEqual('radio1', $_POST['radioInput']);
            $this->assertEqual('Textarea content', $_POST['textAreaInput']);
            $this->assertEqual('text', $_POST['textInput']);
            $this->assertEqual('2012-10', $_POST['weekInput']);
            $this->assertEqual('2012-01', $_POST['weekRange-from']);
            $this->assertEqual('2012-20', $_POST['weekRange-to']);
            
            if (! empty($_POST['submitButton']))
                $this->assertEqual('Submit me', $_POST['submitButton']);
            else
                $this->assertFalse(empty($_POST['imageButton_x']));
        }
    }
    
}
