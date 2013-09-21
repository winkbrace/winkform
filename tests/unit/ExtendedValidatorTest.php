<?php

use Codeception\Util\Stub;
use WinkBrace\WinkForm\Validation\ExtendedValidator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Tests for the Illuminate\Validation\Validator extension ExtendedValidator
 * @author b-deruiter
 *
 */
class ExtendedValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;
    
    /**
     * @var \WinkBrace\WinkForm\Validation\Validator
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
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->passes(), 'array should validate as array');
        
        // and a failing test
        $value = 'string';
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->passes(), 'string should not validate as array');
    }
    
    /**
     * string should validate as not an array
     */
    public function testNotArray()
    {
        $rule = 'not_array';
        
        $value = 'string';
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->passes(), 'string should validate as not an array');
        
        $value = array('one', 'two', 'three');
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->passes(), 'string should not validate as not an array');
    }
    
    /**
     * test boolean validation
     */
    public function testBoolean()
    {
        $rule = 'boolean';
        
        $value = false;
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->passes(), 'boolean should validate as boolean');
        
        $value = 'string';
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->passes(), 'boolean should not validate as boolean');
    }
    
    /**
     * test numeric array validation
     */
    public function testNumericArray()
    {
        $rule = 'numeric_array';
    
        // passes
        $value = array('one', 'two', 'three');
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->passes(), 'numeric array should validate as numeric array');
    
        // fails
        $value = array('key' => 'value', 'foo' => 'bar');
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->passes(), 'assoc array should not validate as numeric array');
        
        // empty array should pass
        $value = array();
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->passes(), 'empty array should validate as numeric array');
        
        // integer should fail
        $value = 10;
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertFalse($validator->passes(), 'integer should not validate as numeric array');
    }
    
    /**
     * test assoc array validation
     */
    public function testAssocArray()
    {
        $rule = 'assoc_array';
    
        // in the code this is actually ! numeric_array, so I'm not going to test all options again
        $value = array('key' => 'value', 'foo' => 'bar');
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->passes(), 'assoc array should validate as assoc array');
        
        // empty array should pass
        $value = array();
        $validator = new ExtendedValidator($this->translator, array($value), array($rule));
        $this->assertTrue($validator->passes(), 'empty array should validate as assoc array');
    }
}
