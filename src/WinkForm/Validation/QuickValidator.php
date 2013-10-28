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
        $this->errors = array();
    }

    /**
     * validate that $value applies to $rules
     * @param string $attribute    name to display in error message
     * @param string $value        the value to test
     * @param string|array $rules  the rules to test against
     * @return boolean
     */
    public function validate($attribute, $value, $rules)
    {
        $rules = $this->checkRules($rules);

        // The way Validator is built we have to create a new instance for every time we validate with this function
        $validator = new WinkValidator($this->translator, array($attribute => $value), array($attribute => $rules));

        // execute validation and store result to return
        $result = $validator->passes();

        foreach ($validator->getMessageBag()->all() as $error)
            $this->errors[$attribute][] = $error;

        return $result;
    }

    /**
     * validate set of rules at once. This is to support the Laravel way of passing array of rules
     * @param array $values    the values to test
     * @param array $rules     the rules to test against
     * @param array $messages  custom error messages
     * @return boolean
     */
    public function validateSet(array $values, array $rules, array $messages = array())
    {
        foreach ($rules as $rule)
            $this->checkRules($rule);

        $validator = new WinkValidator($this->translator, $values, $rules);

        // execute validation and store result to return
        $result = $validator->passes();

        foreach ($validator->getMessageBag()->all() as $attribute => $error)
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
