<?php namespace WinkForm\Validation;

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
     * @var \Illuminate\Translation\Translator
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
     * all validation errors are stored with the name of the Input object as key
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

        // fetched from the documentation on 2013-09-18
        $this->allowedRules = array(
            // default
            'accepted', 'active_url', 'after', 'alpha', 'alpha_dash',
            'alpha_num', 'array', 'before', 'between', 'confirmed', 'date',
            'date_format', 'different', 'email', 'exists', 'image',
            'in', 'integer', 'ip', 'max', 'mimes', 'min', 'not_in',
            'numeric', 'regex', 'required', 'required_if', 'required_with',
            'required_without', 'same', 'size', 'unique', 'url',
            // custom
            'not_array', 'boolean', 'numeric_array', 'assoc_array',
            );
    }

    /**
     * add validation for Input element
     * @param \WinkForm\Input\Input $input
     * @param string|array $rules
     * @param string $message custom message to overwrite default
     * @throws \Exception
     */
    public function addValidation(\WinkForm\Input\Input $input, $rules, $message = null)
    {
        if (is_string($rules))
            $rules = explode('|', $rules);
        
        if (! $this->rulesExist($rules))
            throw new \Exception('Invalid rule "'.implode('|', $rules).'" specified.');
        
        // create entry in validations array for the input if it doesn't yet exist
        if (! array_key_exists($input->getName(), $this->validations))
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
     * @param string $attribute    name to display in error message
     * @param string $value        the value to test
     * @param string|array $rules  the rules to test against
     * @param string $message      custom error message
     * @return boolean
     */
    public function validate($attribute, $value, $rules, $message = null)
    {
        if (is_string($rules))
            $rules = explode('|', $rules);

        if (! $this->rulesExist($rules))
            throw new \Exception('Invalid rule "' . implode('|', $rules) . '" specified.');
        
        // The way Validator is built we have to create a new instance for every time we validate with this function
        $validator = new ExtendedValidator($this->translator, array($attribute => $value), array($attribute => $rules), array($attribute => $message));

        // execute validation and store result to return
        $result = $validator->passes();

        // addValidation() stores errors with Input name as key. This stores all errors at index 0.
        foreach ($validator->getMessageBag()->all() as $error)
            $this->errors[$attribute][] = $error;
        
        return $result;
    }

    /**
     * execute the validations and return the result
     * @return bool
     */
    public function passes()
    {
        $validator = new ExtendedValidator(
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
     * get the errors of given attribute only
     * @param string $name
     * @return array
     */
    public function getAttributeErrors($name)
    {
        if (array_key_exists($name, $this->errors))
            return $this->errors[$name];
        else
            return array();
    }

    /**
     * check if given rule is known in Laravel Validator
     * @param array $rules
     * @return boolean
     */
    public function rulesExist(array $rules)
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
     * @return array
     */
    public function getValidations()
    {
        return $this->validations;
    }

}
