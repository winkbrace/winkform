<?php
use WinkForm\Input\Checkbox;
use WinkForm\Input\TextInput;
use WinkForm\Input\RadioInput;

if (strpos(get_include_path(), 'private') === false)
    set_include_path('/web/www/private'.PATH_SEPARATOR.get_include_path());

require_once 'Query.php';
require_once 'WinkForm/require.php';
require_once 'simpletest/autorun.php';
require_once 'simpletest/web_tester.php';
SimpleTest::prefer(new TextReporter());

class TestAuthentication extends WebTestCase
{
    // TODO: how to make it work regardless of the host
    const reportPage = 'http://10.0.3.190:80/reports/test_report.php';
    const username   = 'bi';
    const password   = 'b13tje';
    
    
    /**
     * Test that we cannot access the report page without authentication
     */
    public function testReportPageRequiresLogin()
    {
        $this->assertTrue($this->get(static::reportPage));
        $this->assertText('Inloggen');
        // $this->assertResponse(401); // would be nice, but 401 is applicable for HTTP authentication
        // $this->showHeaders(); // debugging only
    }
    
    /**
     * Test that we can loggin on the report page
     */
    public function testReportPageLogin()
    {
        $this->get(static::reportPage);
        $this->setField('login_gn', static::username);
        $this->setField('login_ww', static::password);
        $this->click('Inloggen');
        $this->assertText('Test report');
        // $this->showHeaders(); // debugging only
        
        /* Get the entire source of the page and load it in a DOMDodocument */
        $xml = $this->buildXML($this->getBrowser()->getContent());
        $xp  = new DOMXPath($xml);
        
        /**
         * test the text input field inside the report form
         */
    
        // test the expected ID - using setId()
        $this->assertFieldById('text_id');
        
        // test the expected name
        $this->assertFieldByName('text_field');
        
        // test the expected value
        $this->assertFieldValue('text_id', 'Foo', 'Foo', 'Value of the text field is not the expected one.');
        
        // test as XML
        $textInput = $xml->getElementById('text_id');
        
        // test if type of input is text
        $this->assertEqual($textInput->getAttribute('type'), 'text');
        
        // test that the 'onclick' attribute is not set by default
        $this->assertFalse($textInput->hasAttribute('onclick'));
        
        // test that the 'onchange' attribute is not set by default
        $this->assertFalse($textInput->hasAttribute('onchange'));
        
        // test that the 'readonly' attribute is not set by default
        $this->assertFalse($textInput->hasAttribute('readonly'));
        
        // test that the 'disabled' attribute is not set by default
        $this->assertFalse($textInput->hasAttribute('disabled'));
        
        // test that the 'title' attribute is not set by default
        $this->assertFalse($textInput->hasAttribute('title'));
        
        /**
         * test the text input field outside the report form
         */
        
        $outsideTextInput = $xml->getElementById('textInput');
        
        // test that it has the attribute class
        $this->assertTrue($outsideTextInput->hasAttribute('class'));
        
        // test it has only the neccesary classes
        $outsideTextInputClasses = $this->parseClassToArray($outsideTextInput->getAttribute('class'));
        
        // test TextInput::setClass('fooClass')
        $this->assertTrue(in_array('fooClass', $outsideTextInputClasses));
        
        // test TextInput::addClass('barClass');
        $this->assertTrue(in_array('barClass', $outsideTextInputClasses));
        
        // test TextInput::removeClass('bazClass')
        $this->assertFalse(in_array('bazClass', $outsideTextInputClasses));
        
        // test TextInput::setTitle()
        $this->assertEqual($outsideTextInput->getAttribute('title'), 'quux');
        
        // test TextInput::setMaxLength()
        $this->assertEqual($outsideTextInput->getAttribute('maxlength'), 10);
        
        // test the style
        $this->assertTrue($outsideTextInput->hasAttribute('style'));
        
        $style =  $this->parseStyleToArray($outsideTextInput->getAttribute('style'));
        
        // test the setStyle() method
        $this->assertTrue(array_key_exists('text-align', $style));
        $this->assertTrue($style['text-align'] == 'center');
        
        // test TextInput::setWidth()
        $this->assertTrue(array_key_exists('width', $style));
        $this->assertTrue($style['width'] == '50px');
        
        // test TextInput::addStyle()
        $this->assertTrue(array_key_exists('color', $style));
        $this->assertTrue($style['color'] == 'red');
        
        // test TextInput::removeStyle()
        $this->assertFalse(array_key_exists('background-color', $style));
        
        // test that the 'display:none' (hidden) is not set by default
        $this->assertFalse(array_key_exists('display', $style));
        
        // test TextInput::setSelected()
        $this->assertFieldValue('textInput', 'Baz', 'Baz', 'Value of the text field is not the expected one.');
        
        // test TextInput::setLabel()
        $labelTextInput = $outsideTextInput->previousSibling;
        
        // test that the outside text input has a label
        $this->assertEqual('label', $labelTextInput->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelTextInput->getAttribute('for'), 'textInput');
        
        // test the label value
        $this->assertEqual($labelTextInput->nodeValue, 'Qux');
        
        /**
         * test the hidden text input field outside the report form
         */
        
        $outsideHiddenTextInput = $xml->getElementById('hiddenFoo');
        
        $style =  $this->parseStyleToArray($outsideHiddenTextInput->getAttribute('style'));
        
        $this->assertTrue(array_key_exists('display', $style));
        $this->assertTrue($style['display'] == 'none');
        
        // test TextInput::setDisabled()
        $this->assertTrue($outsideHiddenTextInput->hasAttribute('readonly'));
        
        /**
         * test the address input fields outside the report form
         */
        
        $address = array('postcode'   => $xml->getElementById('postcode'),
                         'huisnr'     => $xml->getElementById('huisnr'),
                         'toevoeging' => $xml->getElementById('toevoeging')
                        );
        // each test is performed for each field the address contains
        foreach ($address as $name => $field)
        {
            // test that the type of field is text
            $this->assertEqual($field->getAttribute('type'), 'text');
            
            // test the name value
            $this->assertEqual($field->getAttribute('name'), $name);
        }
        
        // test the script has been added after the last element
        $addressScript = $address['toevoeging']->nextSibling;
        $this->assertEqual('script', $addressScript->nodeName);
        
        /**
         * test the textarea input field outside the report form
         */
        
        $textarea = $xml->getElementById('textAreaFoo');
        
        // test TextAreaInput::setClass()
        $this->assertEqual($textarea->getAttribute('class'), 'bar');
        
        // test TextAreaInput::setDisabled()
        $this->assertTrue($textarea->hasAttribute('disabled'));
        
        // test TextAreaInput::setSelected()
        $this->assertEqual(trim($textarea->nodeValue), 'waldo');
        
        // test TextAreaInput::setTitle()
        $this->assertEqual($textarea->getAttribute('title'), 'qux');
                    
        // test TextAreaInput::setRows()
        $this->assertEqual($textarea->getAttribute('rows'), 10);
        
        // test TextAreaInput::setCols()
        $this->assertEqual($textarea->getAttribute('cols'), 20);
        
        // test the style
        $this->assertTrue($textarea->hasAttribute('style'));
        
        $style = $this->parseStyleToArray($textarea->getAttribute('style'));
        
        // test TextAreaInput::setHidden()
        // $this->assertTrue(array_key_exists('display', $style));
        // $this->assertTrue($style['display'] == 'none');
        
        // test TextAreaInput::setWidth()
        $this->assertTrue(array_key_exists('width', $style));
        $this->assertTrue($style['width'] == '200px');
        
        // test TextAreaInput::setWrapStyle()
        $this->assertTrue(array_key_exists('white-space', $style));
        $this->assertTrue($style['white-space'] == 'pre');
        
        // test TextAreaInput::addStyle()
        $this->assertTrue(array_key_exists('color', $style));
        $this->assertTrue($style['color'] == 'red');
        
        // test TextAreaInput::setLabel()
        $labelTextArea = $textarea->previousSibling;
        
        // test that the outside text area input has a label
        $this->assertEqual('label', $labelTextArea->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelTextArea->getAttribute('for'), 'textAreaFoo');
        
        // test the label value
        $this->assertEqual($labelTextArea->nodeValue, 'baz');
        
        /**
         *  test the week input field outside the report form
         */
        
        $weekInput = $xml->getElementById('weekInput');
        $weekContainer = $xml->getElementById($weekInput->getAttribute('id') . '-container');
        
        // test WeekInput::setSelected()
        $this->assertEqual($weekInput->getAttribute('value'), '2011-39');
        
        foreach ($weekContainer->childNodes as $weekPart)
        {
            if ($weekPart->nodeName != 'select')
                continue;
                            
            $style =  $this->parseStyleToArray($weekPart->getAttribute('style'));
            $classes = $this->parseClassToArray($weekPart->getAttribute('class'));
            
            // test WeekInput::setClass()
            $this->assertTrue(in_array('barClass', $classes));
            
            // test WeekInput::setDisabled()
            $this->assertTrue($weekPart->hasAttribute('disabled'));
            
            // test WeekInput::setHidden()
            $this->assertTrue(array_key_exists('display', $style));
            $this->assertTrue($style['display'] == 'none');

            // test WeekInput::setTitle()
            $this->assertEqual($weekPart->getAttribute('title'), 'quuux');
                        
            // test the style
            $this->assertTrue($weekPart->hasAttribute('style'));
           
            // test WeekInput::setWidth()
            $this->assertTrue(array_key_exists('width', $style));
            
            $width = (stripos($weekPart->getAttribute('id'), 'year') !== false)
                ? round(120 * 0.55, 0, PHP_ROUND_HALF_DOWN)         // 55% of the width for the year
                : 120 - round(120 * 0.55, 0, PHP_ROUND_HALF_DOWN);  // 45% of the width for the week
            $this->assertTrue($style['width'] == $width . 'px');
            
            // test WeekInput::addStyle()
            $this->assertTrue(array_key_exists('color', $style));
            $this->assertTrue($style['color'] == 'blue');
        }
        
        // test WeekInput::setLabel()
        $labelWeekInput = $weekContainer->previousSibling;
        
        // test that the outside text area input has a label
        $this->assertEqual('label', $labelWeekInput->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelWeekInput->getAttribute('for'), 'weekInput');
        
        // test the label value
        $this->assertEqual($labelWeekInput->nodeValue, 'Foo');
        
        /**
         * test the button input field outside the report form
         */
        
        $buttonInput   = $xml->getElementById('button');
        $buttonClasses = $this->parseClassToArray($buttonInput->getAttribute('class'));
        $buttonStyle   = $this->parseStyleToArray($buttonInput->getAttribute('style'));
        
        // test Button::setClass()
        $this->assertTrue(in_array('btn', $buttonClasses));    // default class
        $this->assertTrue(in_array('boo', $buttonClasses));
        
        // test Button::setDisabled()
        $this->assertTrue($buttonInput->hasAttribute('disabled'));
        
        // test Button::setTitle()
        $this->assertEqual($buttonInput->getAttribute('title'), 'bot');
        
        // test Button::setSelected()
        $this->assertEqual($buttonInput->getAttribute('value'), 'Bar');
        
        // test Button::setWidth()
        $this->assertTrue(array_key_exists('width', $buttonStyle));
        $this->assertTrue($buttonStyle['width'] == '120px');
        
        // test Button::addStyle()
        $this->assertTrue(array_key_exists('color', $buttonStyle));
        $this->assertTrue($buttonStyle['color'] == 'fuchsia');
        
        // test Button::setLabel()
        $labelButtonInput = $buttonInput->previousSibling;
        
        // test that the outside text input has a label
        $this->assertEqual('label', $labelButtonInput->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelButtonInput->getAttribute('for'), 'button');
        
        
        /**
         *  test the hidden input field outside the report form
         */
        
        $hiddenInput = $xml->getElementById('hiddenInput');
        $hiddenClasses = $this->parseClassToArray($hiddenInput->getAttribute('class'));
        $hiddenStyle   = $this->parseStyleToArray($hiddenInput->getAttribute('style'));
        
        // test if type of input is hidden
        $this->assertEqual($hiddenInput->getAttribute('type'), 'hidden');
        
        // test HiddenInput::setClass()
        $this->assertTrue(in_array('boo', $buttonClasses));
        
        // test HiddenInput::setDisabled()
        $this->assertTrue($hiddenInput->hasAttribute('disabled'));
        
        // test HiddenInput::setTitle()
        $this->assertEqual($hiddenInput->getAttribute('title'), 'Goo');
        
        // test HiddenInput::setSelected()
        $this->assertEqual($hiddenInput->getAttribute('value'), 'voo');
        
        // test HiddenInput::setWidth()
        $this->assertTrue(array_key_exists('width', $hiddenStyle));
        $this->assertTrue($hiddenStyle['width'] == '30px');
        
        // test HiddenInput::addStyle()
        $this->assertTrue(array_key_exists('color', $hiddenStyle));
        $this->assertTrue($hiddenStyle['color'] == 'midnightblue');

        /**
         * test the file input field outside the report form
         */
    
        $fileInput   = $xml->getElementById('fileInput');
        $fileClasses = $this->parseClassToArray($fileInput->getAttribute('class'));
        $fileStyle   = $this->parseStyleToArray($fileInput->getAttribute('style'));
        
        // test if type of input is file
        $this->assertEqual($fileInput->getAttribute('type'), 'file');
        
        // test FileInput::setClass()
        $this->assertTrue(in_array('vuu', $fileClasses));
        
        // test FileInput::setDisabled()
        $this->assertTrue($fileInput->hasAttribute('disabled'));
        
        // test FileInput::setTitle()
        $this->assertEqual($fileInput->getAttribute('title'), 'Sii');
        
        // test FileInput::setWidth()
        $this->assertTrue(array_key_exists('width', $fileStyle));
        $this->assertTrue($fileStyle['width'] == '190px');
        
        // test FileInput::addStyle()
        $this->assertTrue(array_key_exists('color', $fileStyle));
        $this->assertTrue($fileStyle['color'] == 'brown');

        // test FileInput::setLabel()
        $labelFileInput = $fileInput->previousSibling;
        
        // test that the outside text input has a label
        $this->assertEqual('label', $labelFileInput->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelFileInput->getAttribute('for'), 'fileInput');

        // test the label value
        $this->assertEqual($labelFileInput->nodeValue, 'Fii');
        
        /**
         * test the submit input outside the report form
         */

        $submitInput   = $xml->getElementById('submitButton');
        $submitClasses = $this->parseClassToArray($submitInput->getAttribute('class'));
        $submitStyle   = $this->parseStyleToArray($submitInput->getAttribute('style'));
        
        // test if type of input is submit
        $this->assertEqual($submitInput->getAttribute('type'), 'submit');
        
        // test SubmitButton::setClass()
        $this->assertTrue(in_array('btn', $submitClasses));    // default class
        $this->assertTrue(in_array('sub', $submitClasses));
        
        // test SubmitButton::setDisabled()
        $this->assertTrue($submitInput->hasAttribute('disabled'));
        
        // test SubmitButton::setTitle()
        $this->assertEqual($submitInput->getAttribute('title'), 'tuu');
        
        // test SubmitButton::setWidth()
        $this->assertTrue(array_key_exists('width', $submitStyle));
        $this->assertTrue($submitStyle['width'] == '170px');
        
        // test SubmitButton::addStyle()
        $this->assertTrue(array_key_exists('color', $submitStyle));
        $this->assertTrue($submitStyle['color'] == 'silver');
            
        // test SubmitButton::setSelected()
        $this->assertEqual($weekInput->getAttribute('value'), '2011-39');
        
        // test SubmitButton::setLabel()
        $labelSubmitInput = $submitInput->previousSibling;
        
        // test that the outside text input has a label
        $this->assertEqual('label', $labelSubmitInput->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelSubmitInput->getAttribute('for'), 'submitButton');
        
        // test the label value
        $this->assertEqual($labelSubmitInput->nodeValue, 'Luu');
        
        /**
         * test the image submit input outside the report form
         */
        
        $imageInput   = $xml->getElementById('imageButton');
        $imageClasses = $this->parseClassToArray($imageInput->getAttribute('class'));
        $imageStyle   = $this->parseStyleToArray($imageInput->getAttribute('style'));
        
        // test if type of input is image
        $this->assertEqual($imageInput->getAttribute('type'), 'image');
        
        // test ImageButton::setClass()
        $this->assertTrue(in_array('buu', $imageClasses));
        
        // test ImageButton::setTitle()
        $this->assertEqual($imageInput->getAttribute('title'), 'Tao');
        
        // test ImageButton::setWidth()
        $this->assertTrue(array_key_exists('width', $imageStyle));
        $this->assertTrue($imageStyle['width'] == '130px');
        
        // test ImageButton::addStyle()
        $this->assertTrue(array_key_exists('color', $imageStyle));
        $this->assertTrue($imageStyle['color'] == 'violet');
         
        // test ImageButton::setLabel()
        $labelImageInput = $imageInput->previousSibling;
        
        // test that the outside text input has a label
        $this->assertEqual('label', $labelImageInput->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelImageInput->getAttribute('for'), 'imageButton');
        
        // test the label value
        $this->assertEqual($labelImageInput->nodeValue, 'Nao');
        
        /**
         * test the radio input outside the report form
         */
        
        $radioContainer = $xml->getElementById('radioInput-container');
        $radioStyle     = $this->parseStyleToArray($radioContainer->getAttribute('style'));
        $radioValues    = array(1 => 'one', 2 => 'two', 3 => 'three');
        
        // test RadioInput::setWidth()
        $this->assertTrue(array_key_exists('width', $radioStyle));
        $this->assertTrue($radioStyle['width'] == '220px');
        
        // test RadioInput::addStyle()
        $this->assertTrue(array_key_exists('color', $radioStyle));
        $this->assertTrue($radioStyle['color'] == 'coral');
        
        // test RadioInput::setLabel()
        $labelRadioInput = $radioContainer->previousSibling;
        
        // test that the outside text input has a label
        $this->assertEqual('label', $labelRadioInput->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($labelRadioInput->getAttribute('for'), 'radioInput');
        
        // test the label value
        $this->assertEqual($labelRadioInput->nodeValue, 'Lee');
        
        // test RadioInput::setRenderInColumns()
        $result = $xp->evaluate('//div[@id="radioInput-container"]/table//td');
        $this->assertEqual($result->length, 2); // 2 columns
        // for each radio input =>  1 input + 1 label + 1 <br> = 3 elements
        $this->assertEqual($result->item(0)->childNodes->length, 6); // 2 radio buttons in the first column
        $this->assertEqual($result->item(1)->childNodes->length, 3); // 1 radio button in the first column
        
        foreach ($radioContainer->childNodes as $radioInput)
        {
            if ($radioInput->nodeName != 'input')
                continue;
            
            $radioClasses = $this->parseClassToArray($radioInput->getAttribute('class'));
            
            // test if type of input is radio
            $this->assertEqual($radioInput->getAttribute('type'), 'radio');
                
            // test RadioInput::setDisabled()
            $this->assertTrue($radioInput->hasAttribute('disabled'));
            
            // test RadioInput::setClass()
            $this->assertTrue(in_array('cee', $radioClasses));
            
            // test RadioInput::setTitle()
            $this->assertEqual($radioInput->getAttribute('title'), 'Bee');
            
            // test RadioInput::setLabel()
            $labelRadioInput = $radioInput->nextSibling;
            
            // test that the radio input has a label
            $this->assertEqual('label', $labelRadioInput->nodeName);
                
            // test RadioInput::appendOptions()
            $this->assertTrue(array_key_exists($radioInput->getAttribute('value'), $radioValues));
            $this->assertEqual($labelRadioInput->nodeValue, $radioValues[$radioInput->getAttribute('value')]);
            
            // test RadioInput::setSelected()
            if ($radioInput->getAttribute('value') == 2)
                $this->assertTrue($radioInput->hasAttribute('checked'));
        }
        
        /**
         * test the week range outside the report form
         */
        
        $weekRangeSelectors = array('from' => 'Between', 'to' => 'and');
        $weekRangeValues    = array('from' => '2012-01', 'to' => '2012-20');
        
        foreach ($weekRangeSelectors as $selector => $label)
        {
            $weekRange  = $xml->getElementById('weekRange-' . $selector . '-container');
            $weekStyle  = $this->parseStyleToArray($weekRange->getAttribute('style'));
            $weekLabel  = $weekRange->previousSibling;
            $weekScript = $weekRange->nextSibling;
            
            // test the script
            foreach(array('week', 'year') as $type)
                $this->assertTrue(strpos($weekScript->nodeValue, 'weekRange-' . $selector . '-' . $type) !== false);
         
            // test WeekRange::setWidth()
            $this->assertTrue(array_key_exists('width', $weekStyle));
            $this->assertTrue($weekStyle['width'] == round(230 / 2, 0, PHP_ROUND_HALF_DOWN) . 'px'); // half of WeekRange::setWidth()
         
            // test WeekRange::addStyle()
            $this->assertTrue(array_key_exists('color', $weekStyle));
            $this->assertTrue($weekStyle['color'] == 'darkcyan');
            
            // test that the week range has a label
            $this->assertEqual('label', $weekLabel->nodeName);
            
            // test the label is set for the right text input field
            $this->assertEqual($weekLabel->getAttribute('for'), 'weekRange-' . $selector);
            
            // test the label value
            $this->assertEqual($weekLabel->nodeValue, $label);
            
            // each week range is composed of two select inputs and a script
            foreach ($weekRange->childNodes as $weekRangeComponent)
            {
                if ($weekRangeComponent->nodeName == 'select')
                {
                    $weekRangeComponentClasses = $this->parseClassToArray($weekRangeComponent->getAttribute('class'));
                    $weekRangeComponentStyle   = $this->parseStyleToArray($weekRangeComponent->getAttribute('style'));
                    
                    $weekRangeComponentIds = array('weekRange-' . $selector . '-week', 'weekRange-' . $selector . '-year');
                    $this->assertTrue(in_array($weekRangeComponent->getAttribute('id'), $weekRangeComponentIds));
                    
                    // test WeekRange::setClass()
                    $this->assertTrue(in_array('waa', $weekRangeComponentClasses));
                    
                    // test WeekRange::setDisabled()
                    $this->assertTrue($weekRangeComponent->hasAttribute('disabled'));
                    
                    // test WeekInput::setWidth()
                    $width = (stripos($weekRangeComponent->getAttribute('id'), 'year') !== false)
                        ? round(115 * 0.55, 0, PHP_ROUND_HALF_DOWN)         // 55% of the width for the year
                        : 115 - round(115 * 0.55, 0, PHP_ROUND_HALF_DOWN);  // 45% of the width for the week
                    $this->assertTrue($weekRangeComponentStyle['width'] == $width . 'px');
                    
                    // test the new WeekRange() constructer, that the right values have been selected
                    $result = $xp->evaluate('string(//select[@id="' . $weekRangeComponent->getAttribute('id') . '"]/option[@selected="selected"])');
                    
                    list($weekRangeYear, $weekRangeMonth) = explode('-', $weekRangeValues[$selector]);
                    $selected = (stripos($weekRangeComponent->getAttribute('id'), 'year') !== false)
                        ? $weekRangeYear
                        : $weekRangeMonth;
                    
                    $this->assertEqual($selected, $result);
                   
                    // test WeekRange::addStyle()
                    $this->assertTrue(array_key_exists('color', $weekRangeComponentStyle));
                    $this->assertTrue($weekRangeComponentStyle['color'] == 'darkcyan');
                    
                    // test WeekRange::setTitle()
                    $this->assertEqual($weekRangeComponent->getAttribute('title'), 'taa');
                    
                }
                elseif ($weekRangeComponent->nodeName == 'input')
                {
                    // test if type of input is hidden
                    $this->assertEqual($weekRangeComponent->getAttribute('type'), 'hidden');
                    $this->assertEqual($weekRangeComponent->getAttribute('value'), $weekRangeValues[$selector]);
                }
            }
        }
       
        /**
         * test the date input field outside the report form
         */
       
        // @todo how to test the image that has been added by jQuery UI?
        $dateInput   = $xml->getElementById('dateInput');
        $dateClasses = $this->parseClassToArray($dateInput->getAttribute('class'));
        $dateStyle   = $this->parseStyleToArray($dateInput->getAttribute('style'));
        $dateLabel   = $dateInput->previousSibling;
        $dateScript  = $dateInput->parentNode->nextSibling;
        
        // test if type of input is text
        $this->assertEqual($dateInput->getAttribute('type'), 'text');
       
        // test DateInput::setClass()
        $this->assertTrue(in_array('duu', $dateClasses));
        
        // test DateInput::setSelected()
        $this->assertEqual($dateInput->getAttribute('value'), '10-03-1979');
        
        // test DateInput::setTitle()
        $this->assertEqual($dateInput->getAttribute('title'), 'tuu');
        
        // test that the outside date input has a label
        $this->assertEqual('label', $dateLabel->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($dateLabel->getAttribute('for'), 'dateInput');
        
        // test the label value
        $this->assertEqual($dateLabel->nodeValue, 'Luu');
        
        // test that the JS is initialised
        $this->assertEqual('script', $dateScript->nodeName);
        $this->assertEqual('text/javascript', $dateScript->getAttribute('type'));
        
        // test that it's the right JS
        $this->assertTrue(stripos($dateScript->nodeValue, '$("#dateInput").datepicker(options);') !== false);
        
        /**
         * test the date input field outside the report form
         */
        
        $monthInput = $xml->getElementById('mooo');
        $monthInputYear = $monthInput->previousSibling;
        $monthInputMonth = $monthInputYear->previousSibling;
        $monthInputValue = '2013-05';
        list($_year, $_month) = explode('-', $monthInputValue);
        
        // test MonthInput::setSelected()
        $this->assertEqual($monthInputValue, $monthInput->getAttribute('value'));
        
        $expected = $xp->evaluate('string(//select[@id="' . $monthInput->getAttribute('id') . '_month' . '"]/option[@selected="selected"]/@value)');
        $this->assertEqual($_month, $expected);
        
        $expected = $xp->evaluate('string(//select[@id="' . $monthInput->getAttribute('id') . '_year' . '"]/option[@selected="selected"]/@value)');
        $this->assertEqual($_year, $expected);
        
        // test that MonthInput has a hidden input field
        $this->assertEqual('input', $monthInput->nodeName);
        $this->assertEqual('hidden', $monthInput->getAttribute('type'));
        
        
        /**
         * test the single dropdown select outside the report form
         */
        
        $dropdownSingle = $xml->getElementById('dropdownSingle');
        $dropdownSingleValues = array(
                                    'dd1' => 'dropdown1',
                                    'dd2' => 'dropdown2',
                                    'dd3' => 'dropdown3',
                                  );
        
        // test Dropdown::appendOptions()
        $this->assertEqual(3, $dropdownSingle->childNodes->length);
        foreach ($dropdownSingle->childNodes as $dropdownSingleOption)
        {
            $this->assertTrue(array_key_exists($dropdownSingleOption->getAttribute('value'), $dropdownSingleValues));
            $this->assertTrue($dropdownSingleOption->nodeValue == $dropdownSingleValues[$dropdownSingleOption->getAttribute('value')]);
        }
        
        // test Dropdown::setSelected()
        $expected = $xp->evaluate('string(//select[@id="' . $dropdownSingle->getAttribute('id') . '"]/option[@selected="selected"]/@value)');
        $this->assertEqual('dd2', $expected);
        
        /**
         * test the single dropdown select outside the report form
         */
       
        $dropdownMultiple = $xml->getElementById('dropdownMultiple');
        $dropdownMultipleValues = array(
                                        'dd1' => 'dropdown1',
                                        'dd2' => 'dropdown2',
                                        'dd3' => 'dropdown3',
                                        'dd4' => 'dropdown4',
                                    );
        
        // test Dropdown::setMultiple()
        $this->assertTrue($dropdownMultiple->hasAttribute('multiple'));
        
        // test Dropdown::appendOptions()
        $this->assertEqual(4, $dropdownMultiple->childNodes->length);
        foreach ($dropdownMultiple->childNodes as $dropdownMultipleOption)
        {
            $this->assertTrue(array_key_exists($dropdownMultipleOption->getAttribute('value'), $dropdownMultipleValues));
            $this->assertTrue($dropdownMultipleOption->nodeValue == $dropdownMultipleValues[$dropdownMultipleOption->getAttribute('value')]);
        }
        
        // test Dropdown::setSelected()
        $dropdownMultipleSelected = $xp->evaluate('//select[@id="'
                                  . $dropdownMultiple->getAttribute('id')
                                  . '"]/option[@selected="selected"]/@value');
        $this->assertEqual(2, $dropdownMultipleSelected->length);  // 2 items selected
        foreach ($dropdownMultipleSelected as $dropdownMultipleSelected)
            $this->assertTrue(in_array($dropdownMultipleSelected->nodeValue, array('dd2', 'dd3'))); // the selected items
        
        /**
         *  test the checkbox outside the report form
         */
        
        $checkboxes = $xml->getElementById('checkbox' . '-container');
        $checkboxesValues = array(
                                'cb1' => 'checkbox1',
                                'cb2' => 'checkbox2',
                                'cb3' => 'checkbox3',
                             );
        $checkboxesLabel = $checkboxes->previousSibling;
        $checkboxesStyle = $this->parseStyleToArray($checkboxes->getAttribute('style'));
        
        // test Checkbox::setWidth()
        $this->assertTrue(array_key_exists('width', $checkboxesStyle));
        $this->assertTrue($checkboxesStyle['width'] == '200px');
        
        // test Checkbox::addStyle()
        $this->assertTrue(array_key_exists('color', $checkboxesStyle));
        $this->assertTrue($checkboxesStyle['color'] == 'purple');
        
        // test Checkbox::setSelected()
        $checkboxesSelected = $xp->evaluate('//div[@id="' . 'checkbox' . '-container' . '"]/input[@checked="checked"]/@value');
        $this->assertEqual(2, $checkboxesSelected->length);  // 2 checkboxes selected
        foreach ($checkboxesSelected as $checkboxSelected)
            $this->assertTrue(in_array($checkboxSelected->nodeValue, array('cb1', 'cb3'))); // the selected items
        
        foreach ($checkboxes->childNodes as $checkbox)
        {
            if ($checkbox->nodeName != 'input')
                continue;
            
            $checkboxLabel = $checkbox->nextSibling;
            
            // test that the type is checkbox
            $this->assertEqual('checkbox', $checkbox->getAttribute('type'));
            
            // test that they are grouped and will be posted as an array
            $this->assertEqual('checkbox[]', $checkbox->getAttribute('name'));
            
            // test Checkbox::setDisabled()
            $this->assertTrue($checkbox->hasAttribute('disabled'));
            
            // test Checkbox::setTitle()
            $this->assertEqual('chuu', $checkbox->getAttribute('title'));
            
            // test the label is set for the right checkbox
            $this->assertEqual($checkboxLabel->getAttribute('for'), $checkbox->getAttribute('id'));
            
            // test Checkbox::appendOptions()
            $this->assertTrue(array_key_exists($checkbox->getAttribute('value'), $checkboxesValues));
            $this->assertEqual($checkboxLabel->nodeValue, $checkboxesValues[$checkbox->getAttribute('value')]);
        }
        
        // test Checkbox::setLabel()
        $this->assertEqual('label', $checkboxesLabel->nodeName);
        
        // test the label is set for the right text input field
        $this->assertEqual($checkboxesLabel->getAttribute('for'), 'checkbox');
        
        // test the label value
        $this->assertEqual($checkboxesLabel->nodeValue, 'chii');
        
        /**
         * test the date range outside the report form
         */
        
        $dateRangeSelectors = array('from' => 'van', 'to' => 'tot');
        $dateRangeValues    = array('from' => '01-01-2012', 'to' => '31-01-2012');
        
        foreach ($dateRangeSelectors as $selector => $label)
        {
            $dateRange  = $xml->getElementById('dateRange-' . $selector . '-container');
            $dateStyle  = $this->parseStyleToArray($dateRange->getAttribute('style'));
            $dateScript = $dateRange->nextSibling;
            
            // test that the JS is initialised
            $this->assertEqual('script', $dateScript->nodeName);
            $this->assertEqual('text/javascript', $dateScript->getAttribute('type'));
            
            // test that it's the right JS
            $this->assertTrue(stripos($dateScript->nodeValue, '$("#dateRange-' . $selector . '").datepicker(options);') !== false);
            
            // test default float left
            $this->assertTrue(array_key_exists('float', $dateStyle));
            $this->assertTrue($dateStyle['float'] == 'left');
            
            // each date input is composed of a select input and a label
            foreach ($dateRange->childNodes as $dateRangeComponent)
            {
                if ($dateRangeComponent->nodeName != 'input')
                    continue;
                
                $dateRangeComponentStyle   = $this->parseStyleToArray($dateRangeComponent->getAttribute('style'));
                $dateRangeComponentClasses = $this->parseClassToArray($dateRangeComponent->getAttribute('class'));
                $dateRangeComponentLabel   = $dateRangeComponent->previousSibling;
                
                // test DateRange::setClass()
                $this->assertTrue(in_array('traa', $dateRangeComponentClasses));
                
                // test DateRange::addStyle()
                $this->assertTrue(array_key_exists('color', $dateRangeComponentStyle));
                $this->assertTrue($dateRangeComponentStyle['color'] == 'sienna');
                
                // test that DateInput default width is 80px
                $this->assertTrue(array_key_exists('width', $dateRangeComponentStyle));
                $this->assertTrue($dateRangeComponentStyle['width'] == '80px');
                
                // test that DateInput maxlength is 10
                $this->assertEqual(10, $dateRangeComponent->getAttribute('maxlength'));
                
                // test DateRange::setTitle()
                $this->assertEqual($dateRangeComponent->getAttribute('title'), 'tbii');
                
                // test from and to values passed to the constructor
                $this->assertEqual($dateRangeComponent->getAttribute('value'), $dateRangeValues[$selector]);
                
                // test the label is set for the right date input
                $this->assertEqual($dateRangeComponentLabel->getAttribute('for'), $dateRangeComponent->getAttribute('id'));
                
                // test DateRange::->setLabels()
                $this->assertEqual($dateRangeComponentLabel->nodeValue, $dateRangeSelectors[$selector]);
            }
        }
        
        
        // TODO: finish testing the post values (only submit by ID seems to work)
        /*$post = $this->submitFormById('test_report_form');
        
        print($this->getBrowser()->getRequest());
        $this->showHeaders();*/
            
        // test a radio input that has been passed a bad label and value
        // passing anything other than array should invalidate the validator and not set the Input array values
            // @TODO they seem to fail even though the text is visible on the page
            // $this->assertText('Error rendering RadioInput object with name broken');
            // $this->assertText('broken_value Given value has to be an array');
            // $this->assertText('broken_label Given value has to be an array');
            
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
        $this->assertEqual(array('val1','val2'), $input->getValues());
    
        $input->setLabels(array('label1','label2'));
        $this->assertEqual(array('label1','label2'), $input->getLabels());
    
        $input->appendOption('val3', 'label3');
        $this->assertEqual(array('val1','val2','val3'), $input->getValues());
        $this->assertEqual(array('label1','label2','label3'), $input->getLabels());
    
        $input->appendOptions(array(
            'val4' => 'label4',
            'val5' => 'label5',
        ));
        $this->assertEqual(array('val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertEqual(array('label1','label2','label3','label4','label5'), $input->getLabels());
    
        $input->prependOption('val0', 'label0');
        $this->assertEqual(array('val0','val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertEqual(array('label0','label1','label2','label3','label4','label5'), $input->getLabels());
    
        $input->prependOptions(array('pre0' => 'prealabel0', 'pre1' => 'prealabel1'));
        $this->assertEqual(array('pre0','pre1','val0','val1','val2','val3','val4','val5'), $input->getValues());
        $this->assertEqual(array('prealabel0','prealabel1','label0','label1','label2','label3','label4','label5'), $input->getLabels());
    
        $query = new Query("select 'val6' value, 'label6' label from dual");
        $input->appendOptions($query->fetchArray());
        $this->assertEqual(array('pre0','pre1','val0','val1','val2','val3','val4','val5','val6'), $input->getValues());
        $this->assertEqual(array('prealabel0','prealabel1','label0','label1','label2','label3','label4','label5','label6'), $input->getLabels());
    }
    
    /**
     * test that inputs in a reportform get the required attributes
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
     * checkbox specific tests
     */
    public function testCheckbox()
    {
        $input = new Checkbox('test');
        
        $this->assertTrue(is_null($input->getRenderInColumns()));
        $this->assertEqual($input::ORIENTATION_HORIZONTAL, $input->getOrientation());
        
        $input->setOrientation($input::ORIENTATION_VERTICAL);
        $this->assertEqual(1, $input->getRenderInColumns());
        
        $input->setOrientation($input::ORIENTATION_HORIZONTAL);
        $this->assertEqual(0, $input->getRenderInColumns());
        
        $input->setRenderInColumns(2);
        $this->assertEqual(2, $input->getRenderInColumns());
    }
    
    /**
     * test building the DOM xml
     * @param string $source
     * @return DOMDocument
     */
    protected function buildXML($source)
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        
        // the preserverWhiteSpace doesn't seem to suffice all the time
        // so strip all the white space between tags (might be a win issue)
        $source = preg_replace('/>\s+</', "><", $source);
        
        try
        {
            $xml->loadHTML($source);
        }
        catch (Exception  $e)
        {
            $this->assertTrue(false, $e->getMessage());
        }

        return $xml;
    }
    
    /**
     * Parses a string an returns an array where the key is the CSS attribute
     * and the value is the CSS atribute's value
     *
     * @param string $style
     * @return array
     */
    public function parseStyleToArray($style)
    {
        if (empty($style))
            return array();
        
        $params = array();
        
        $style = rtrim($style, ';');
        
        $elements = explode(';', $style);
        $elements = array_map('trim', $elements);
        
        foreach($elements as $element)
        {
            list($key, $value) = explode(':', $element);
            $params[$key] = trim($value);
        }
        
        return $params;
    }
    
    /**
     * Parses a string an returns an array of classes
     *
     * @param string $string
     * @return array
     */
    public function parseClassToArray($string)
    {
        if (empty($string))
            return array();
        
        $classes = explode(' ', $string);
        $classes = array_filter($classes);
        $classes = array_map('trim', $classes);
        
        return $classes;
    }

}
