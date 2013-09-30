<?php namespace WinkForm\Validation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

/**
 * Abstract Validation class that utilizes Laravel Validation
 * @author b-deruiter
 *
 */
abstract class AbstractValidator
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
    protected $allowedRules = array();

    /**
     * all validation errors are stored with the name of the Input object as key
     * @var array
     */
    protected $errors;


    /**
     * create Validator
     */
    public function __construct()
    {
        $config = require WINKFORM_PATH.'config.php';

        $this->locale = $config['locale'];

        // To display the laravel Validator error messages, the Translator is required.
        // We create a translator object that searches for files in the 'lang' folder
        // lang/{locale}/{domain}.php
        // by default: lang/en/validation.php
        // you can download the default validation.php in your language at
        // @see https://github.com/caouecs/Laravel4-lang
        $this->translator = new Translator(new FileLoader(new Filesystem, $config['lang_location']), $this->locale);

        // fetch the allowed rules by reading the Validator validate methods
        $this->fetchRules();

        // init
        $this->init();
    }

    /**
     * init variables
     */
    abstract protected function init();

    /**
     * reset validations
     */
    public function reset()
    {
        $this->init();
    }

    /**
     * fetch all defined validation rules
     */
    protected function fetchRules()
    {
        $rf = new \ReflectionClass('\WinkForm\Validation\WinkValidator');
        foreach ($rf->getMethods(\ReflectionProperty::IS_PROTECTED) as $prop)
        {
            $name = $prop->getName();
            if (substr($name, 0, 8) == 'validate' && $name != 'validate')
                $this->allowedRules[] = snake_case(substr($name, 8));
        }

        sort($this->allowedRules);
    }

    /**
     * check rules and return array
     * @param string|array $rules
     * @return array $rules
     * @throws \Exception
     */
    protected function checkRules($rules)
    {
        if (is_string($rules))
            $rules = explode('|', $rules);

        if (! $this->rulesExist($rules))
            throw new \Exception('Invalid rule "' . implode('|', $rules) . '" specified.');

        return $rules;
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

}
