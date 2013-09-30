<?php namespace WinkForm\Validation;

class ValidationException extends \Exception
{


    /**
     * Overridden constructor to require a message and errors array
     * @param string $message
     * @param array $errors
     */
    function __construct($message, $errors)
    {
        // start with erorr
        if (! empty($message))
        {
            $message = '<p class="validation-error-message">'.$message.'</p>'.PHP_EOL;
        }
        
        // create validation error list
        $list = '<ul class="validation-errors">'.PHP_EOL;
        foreach ($errors as $name => $errorMessages)
        {
            $error = implode("<br/>\n", $errorMessages);
            $list .= '<li>'.$name.': '.$error.'</li>'.PHP_EOL;
        }
        
        $list .= '</ul>'.PHP_EOL;
        
        // throw the actual Excpetion
        parent::__construct($message.$list);
    }
}
