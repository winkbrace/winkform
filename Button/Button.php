<?php namespace WinkForm\Button;

/**
 * The difference between a <input type="button"> and a <button> element is that
 * inside the <button> element you can put content, providing more flexibility.
 * @author m-nedelcu
 *
 */
class Button extends \WinkForm\Input\Input
{
    /**
     * Always specify the type attribute since browsers have different default values
     * @var string
     */
    protected $type = 'submit';
    
    /**
     * construct Button
     * @param string $name
     * @param mixed $value
     */
    function __construct($name, $value = null)
    {
        parent::__construct($name, $value);
    
        // always set btn class
        $this->addClass('btn');
    }
    
    /**
     * render the button
     */
    public function render()
    {
        // default validity check
        $this->checkValidity();
    
        $output = $this->renderLabel()
        . '<button'
            . $this->renderType()
            . $this->renderId()
            . $this->renderClass()
            . $this->renderName()
            . $this->renderValue()
            . $this->renderStyle()
            . $this->renderDisabled()
            . $this->renderTitle()
            . $this->renderDataAttributes()
            . $this->renderRequired()
            . $this->renderAutoFocus()
            .' >'
            . $this->getValue()
            . '</button>'
            . PHP_EOL;
    
        $output .= $this->renderInvalidations();
    
        return $output;
    }
    
    /**
     * Set the type of the button
     *
     * @param string $type the type of button
     * <ul>
     *   <li>button</li>
     *   <li>reset</li>
     *   <li>submit (default value)</li>
     * </ul>
     * @return \WinkForm\Button\Button
     */
    public function setType($type)
    {
        $allowedTypes = array('button', 'reset', 'submit');
        
        if (in_array($type, $allowedTypes))
        {
            $this->type = $type;
        }
        
        return $this;
    }
}
