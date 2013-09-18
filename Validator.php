<?php namespace WinkForm;

/**
 * Validation class that utilizes Laravel Validation
 * @author b-deruiter
 *
 */
class Validator
{
    /**
     * @var WinkForm\Validator
     */
    protected static $instance = null;
    
    /**
     * singleton factory method
     */
    public static function getInstance()
    {
        if (is_null(static::$instance))
            static::$instance = new Validator();
        
        return static::$instance;
    }
    
    
    protected $validations;
    
    
    /**
     * create Validator
     */
    protected function __construct()
    {
        $this->validations = array();
    }
    
    /**
     * add validation for Input element
     * @param WinkForm\Input\Input $input
     * @param string|array $rules
     * @param string $message
     * @throws \Exception
     */
    public function addValidation(Input\Input $input, $rules, $message = null)
    {
        if (! $this->rulesExist($rules))
            throw new \Exception('Invalid rules "'.$rules.'" specified.');
        
        $this->validations[$input->getName()] = array(
            'data' => $input->getPosted(),
            'rules' => $rules,
            'message' => $message
            );
    }
    
    /**
     * @return array $validations
     */
    public function getValidations()
    {
        return $this->validations;
    }
    
}
