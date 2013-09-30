<?php
use Codeception\Util\Stub;
use WinkForm\Button\Button;
use WinkForm\Button\ResetButton;
use WinkForm\Button\ImageButton;
use WinkForm\Button\SubmitButton;
use WinkForm\Button\InputButton;

/**
 * test the Input class methods
 * @author b-deruiter
 *
 */
class ButtonTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $codeGuy;
    
    /**
     * @var Button
     */
    protected $button;
    
    /**
     * @var InputButton
     */
    protected $inputButton;
    
    /**
     *
     * @var ImageButton
     */
    protected $imageButton;
    
    /**
     *
     * @var ResetButton
     */
    protected $resetButton;
    
    /**
     *
     * @var SubmitButton
     */
    protected $submitButton;

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_before()
     */
    protected function _before()
    {
        $this->button      = new Button('fee', 'bee');
        $this->inputButton = new InputButton('foo', 'boo');
        $this->imageButton = new ImageButton('faa', 'baa');
        $this->resetButton = new ResetButton('fuu', 'buu');
        $this->submitButton = new SubmitButton('fii', 'bii');
    }

    /**
     * (non-PHPdoc)
     * @see \Codeception\TestCase\Test::_after()
     */
    protected function _after()
    {
    }

    /**
     * Test that the input buttons and buttons are generated with the correct class
     */
    public function testCreation()
    {
        $this->assertInstanceOf('\WinkForm\Button\Button', $this->button);
        
        $this->assertInstanceOf('\WinkForm\Button\InputButton', $this->inputButton);
        
        $this->assertInstanceOf('\WinkForm\Button\ImageButton', $this->imageButton);
        
        $this->assertInstanceOf('\WinkForm\Button\ResetButton', $this->resetButton);
        
        $this->assertInstanceOf('\WinkForm\Button\SubmitButton', $this->submitButton);
    }
}
