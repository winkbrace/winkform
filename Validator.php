<?php namespace WinkBrace\WinkForm;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Validation class that utilizes Laravel Validation
 * @author b-deruiter
 *
 */
class Validator
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $validations;

    /**
     * @var array
     */
    protected $allowedRules;

    /**
     * @var bool
     */
    protected $isValid;

    /**
     * @var array
     */
    protected $errors;

    
    /**
     * create Validator
     */
    public function __construct($locale = 'en')
    {
        $this->locale = $locale;

        // To display the laravel Validator error messages, the Translator is required.
        // We create a translator object that searches for files in the 'lang' folder
        // lang/{locale}/{domain}.php
        // by default: lang/en/validation.php
        // you can download the default validation.php in your language at
        // @see https://github.com/caouecs/Laravel4-lang
        $this->translator = new Translator(new FileLoader(new Filesystem, "lang"), $this->locale);

        // init
        $this->validations = array();
        $this->isValid = true;
        $this->errors = array();

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
     * @param \WinkBrace\WinkForm\Input\Input $input
     * @param string|array $rules
     * @param string $message custom message to overwrite default
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
            throw new \Exception('Invalid rule "' . implode('|', $rules) . '" specified.');
        
        // The way Validator is built we have to create a new instance for everytime we validate with this function
        $validator = new \Illuminate\Validation\Validator(
            $this->translator,
            array($value),
            array($rules)
        );

        return $validator->passes();
    }

    /**
     * @return bool
     */
    public function passes()
    {
        $validator = new \Illuminate\Validation\Validator(
            $this->translator,
            $this->getValidationData(),
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->isValid = $validator->passes();
        $this->errors = $validator->getMessageBag()->getMessages();

        return $this->isValid;
    }

    /**
     * @return array
     */
    protected function getValidationData()
    {
        $data = array();
        foreach ($this->validations as $key => $validation)
            $data[$key] = $validation['data'];

        return $data;
    }

    /**
     * @return array
     */
    protected function getValidationRules()
    {
        $rules = array();
        foreach ($this->validations as $key => $validation)
            $rules[$key] = $validation['rules'];

        return $rules;
    }

    /**
     * @return array
     */
    protected function getValidationMessages()
    {
        $messages = array();
        foreach ($this->validations as $key => $validation)
            $messages[$key] = $validation['message'];

        return $messages;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
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
