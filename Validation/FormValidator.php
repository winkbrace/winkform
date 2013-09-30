<?php namespace WinkForm\Validation;

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
    public function addValidation(\WinkForm\Input\Input $input, $rules, $message = null)
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
            $this->getValidationRules(),
            $this->getValidationMessages()
        );

        $this->isValid = $validator->isValid();
        $this->errors = $validator->getMessageBag()->getMessages();

        return $this->isValid;
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
    public function getValidations()
    {
        return $this->validations;
    }

}
