<?php namespace WinkForm\Validation;

/**
 * Validation class that utilizes Laravel Validation
 * @author b-deruiter
 *
 */
class QuickValidator extends AbstractValidator
{
    /**
     * init variables
     */
    protected function init()
    {
        $this->isValid = true;
        $this->errors = array();
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
        $rules = $this->checkRules($rules);

        // The way Validator is built we have to create a new instance for every time we validate with this function
        $validator = new WinkValidator($this->translator, array($attribute => $value), array($attribute => $rules), array($attribute => $message));

        // execute validation and store result to return
        $result = $validator->passes();

        // addValidation() stores errors with Input name as key. This stores all errors at index 0.
        foreach ($validator->getMessageBag()->all() as $error)
            $this->errors[$attribute][] = $error;

        return $result;
    }

    /**
     * None of the validate() methods created an error
     * @return boolean
     */
    public function isValid()
    {
        return empty($this->errors);
    }

}
