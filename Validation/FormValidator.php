<?php namespace WinkForm\Validation;

use Illuminate\Validation\Validator;
use WinkForm\Input\Input;

/**
 * Validation class that utilizes Laravel Validation
 * @author b-deruiter
 *
 */
class FormValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $validations;

    /**
     * @var bool
     */
    protected $isValid;

    /**
     * init variables
     */
    protected function init()
    {
        $this->validations = array();
        $this->isValid = null;
        $this->errors = array();
    }

    /**
     * add validation for Input element
     * @param \WinkForm\Input\Input $input
     * @param string|array $rules
     * @param string $message custom message to overwrite default
     * @throws \Exception
     */
    public function addValidation(Input $input, $rules, $message = null)
    {
        $rules = $this->checkRules($rules);

        $name = $input->getName();

        // create entry in validations array for the input if it doesn't yet exist
        if (! array_key_exists($name, $this->validations))
        {
            $this->validations[$name] = array(
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
                if (! in_array($rule, $this->validations[$name]['rules']))
                    $this->validations[$name]['rules'][] = $rule;
            }

            // append message
            if (! empty($message))
                $this->validations[$name]['message'] = trim($this->validations[$name]['message'] . ' ' . $message);
        }
    }

    /**
     * execute the validations and return the result
     * This method will always run all validations (again)
     * @return bool
     */
    public function run()
    {
        $validator = new WinkValidator(
            $this->translator,
            $this->getValidationData(),
            $this->getValidationRules()
        );

        $this->isValid = $validator->passes();
        $this->fetchMessages($validator);

        return $this->isValid;
    }

    /**
     * fetch error messages from validator and optionally prepend them with a custom message
     * @param Validator $validator
     */
    protected function fetchMessages(Validator $validator)
    {
        /**
         * $errors array is constructed like: field1 => array(0 => 'error message 1', 1 => 'error message 2' ...)
         * I will prepend the custom error message (added by addValidation()) to this messages array per field
         */
        $this->errors = $validator->getMessageBag()->getMessages();
        foreach ($this->errors as $name => &$messages)
        {
            if (! empty($this->validations[$name]['message']))
            {
                $attributeName = str_replace('_', ' ', $name);
                $customMessage = str_replace(':attribute', $attributeName, $this->validations[$name]['message']);
                array_unshift($messages, $customMessage);
            }
        }
    }

    /**
     * return if all validations for this form pass
     * This method will return the result of the last run validations
     * @return bool
     */
    public function isValid()
    {
        if (is_null($this->isValid))
            $this->run();

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
    public function getValidations()
    {
        return $this->validations;
    }

}
