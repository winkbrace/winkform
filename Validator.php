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
    
    
    protected $validations,
              $allowedRules;
    
    
    /**
     * create Validator
     */
    protected function __construct()
    {
        $this->validations = array();
        
        // fetched from the documentation on 18-09-2013
        $this->allowedRules = array(
            'accepted', 'active_url', 'after', 'alpha', 'alpha_dash',
            'alpha_num', 'before', 'between', 'confirmed', 'date',
            'date_format', 'different', 'email', 'exists', 'image',
            'in', 'integer', 'ip', 'max', 'mimes', 'min', 'not_in',
            'numeric', 'regex', 'required', 'required_if', 'required_with',
            'required_without', 'same', 'size', 'unique', 'url',
            );
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
        if (is_string($rules))
            $rules = explode('|', $rules);
        
        if (! $this->rulesExist($rules))
            throw new \Exception('Invalid rule "'.implode('|', $rules).'" specified.');
        
        // create entry in validations array for the input if it doesn't yet exist
        if (empty($this->validations[$input->getName()]))
        {
            $this->validations[$input->getName()] = array(
                'data' => $input->getPosted(),
                'rules' => $rules,
                'message' => $message
                );
        }
        // else merge the rules and append the message
        else
        {
            // merge rules
            foreach ($rules as $rule)
            {
                if (! in_array($rule, $this->validations[$input->getName()]['rules']))
                    $this->validations[$input->getName()]['rules'][] = $rule;
            }
            
            // append message
            if (! empty($message))
                $this->validations[$input->getName()]['message'] = trim($this->validations[$input->getName()]['message'] . ' ' . $message);
        }
    }
    
    /**
     * validate that $value applies to $rules
     * @param string $value
     * @param string|array $rules
     * @return boolean
     */
    public function validate($value, $rules)
    {
        if (is_string($rules))
            $rules = explode('|', $rules);
        
        if (! $this->rulesExist($rules))
            throw new \Exception('Invalid rule "'.implode('|', $rules).'" specified.');
        
        // TODO build validating against Validator
    }
    
    /**
     * check if given rule is known in Laravel Validator
     * @param array $rules
     * @return boolean
     */
    protected function rulesExist(array $rules)
    {
        foreach ($rules as $rule)
        {
            // cut off everything from the colon
            $rule = strpos($rule, ':') !== false ? substr($rule, 0, strpos($rule, ':')) : $rule;
    
            if (! in_array($rule, $this->allowedRules))
                return false;
        }
    
        return true;
    }
    
    /**
     * @return array $validations
     */
    public function getValidations()
    {
        return $this->validations;
    }
    
}
