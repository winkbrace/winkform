<?php

use Codeception\Util\Stub;
use WinkForm\Validation\WinkValidator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Tests for the Illuminate\Validation\Validator extension WinkValidator
 * @author b-deruiter
 *
 */
class WinkValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;

    /**
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_before()
     */
    protected function _before()
    {
        $this->translator = new Translator(new FileLoader(new Filesystem, 'lang'), 'en');
    }

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_after()
     */
    protected function _after()
    {
        unset($this->translator);
    }

    /**
     * array is not in the documentation, but validateArray() exists in the class
     */
    public function testArray()
    {
        $rule = 'array';

        $value = array('one', 'two', 'three');
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'array should validate as array');

        // and a failing test
        $value = 'string';
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'string should not validate as array');
    }

    /**
     * string should validate as not an array
     */
    public function testNotArray()
    {
        $rule = 'not_array';

        $value = 'string';
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'string should validate as not an array');

        $value = array('one', 'two', 'three');
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'string should not validate as not an array');
    }

    /**
     * test boolean validation
     */
    public function testBoolean()
    {
        $rule = 'boolean';

        $value = false;
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'boolean should validate as boolean');

        $value = 'string';
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'boolean should not validate as boolean');
    }

    /**
     * test numeric array validation
     */
    public function testNumericArray()
    {
        $rule = 'numeric_array';

        // passes
        $value = array('one', 'two', 'three');
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'numeric array should validate as numeric array');

        // fails
        $value = array('key' => 'value', 'foo' => 'bar');
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'assoc array should not validate as numeric array');

        // empty array should pass
        $value = array();
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'empty array should validate as numeric array');

        // integer should fail
        $value = 10;
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'integer should not validate as numeric array');
    }

    /**
     * test assoc array validation
     */
    public function testAssocArray()
    {
        $rule = 'assoc_array';

        // in the code this is actually ! numeric_array, so I'm not going to test all options again
        $value = array('key' => 'value', 'foo' => 'bar');
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'assoc array should validate as assoc array');

        // empty array should pass
        $value = array();
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'empty array should validate as assoc array');
    }

    /**
     * test not empty
     */
    public function testNotEmpty()
    {
        $rule = 'not_empty';

        $value = 1;
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'value 1 should validate as not empty');

        $value = array();
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'empty array should not validate as not empty');
    }

    /**
     * test empty
     */
    public function testEmpty()
    {
        $rule = 'empty';

        $value = 1;
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'value 1 should not validate as empty');

        $value = array();
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'empty array should validate as empty');
    }

    /**
     * test all_in
     */
    public function testAllIn()
    {
        $rule = 'all_in:1,2,3,4,5';

        $value = array(1, 2, 3);
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->isValid(), 'all values in 1,2,3 should be in array 1,2,3,4,5');

        $value = array(1, 10, 3);
        $validator = new WinkValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->isValid(), 'not all values in 1,10,3 should be in array 1,2,3,4,5');
    }

}
