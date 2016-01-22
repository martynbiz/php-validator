<?php

namespace MartynBiz;

/**
*
* Designed for string validation (e.g. form submissions). Allows a single value to be checked
* through a series of conditions.
*
* @category ChainValidator
* @package Validator
* @author Martyn Bissett <martynbissett@yahoo.co.uk>
* @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
* @link https://github.com/martynbiz/php-validator
* @version 1.0
*/
class Validator {

	/**
    * Will contain the errors for the duration of the objects existence
    * @var errors
    */
    protected $errors = [];

    /**
    * Key used to provide error message
    * @var string
    */
    protected $key;

    /**
    * Value of the variable being validated, set in chain()
    * @var string
    */
    protected $value;

    /**
    * This is the params to check. Might be $_POST for example
    * @var array
    */
    protected $params;

    /**
    * When an error is found in a chain, switch of error logging for the remainder of the chain
    * @var errors
    */
    protected $checking = true;

    public function __construct($params=array())
    {
        $this->setParams($params);
    }

    /**
    * Log an error
    * @param string $error_message human readable error message
    * @param integer $error_code (optional) can set a numeric value for this type of error
    * @return object Returns this to allow chaining.
    */
    public function logError($key, $message)
    {
        // Log a new error
        $this->errors[$key] = $message;

        // we have tracked a log, close the log for the rest of the chain
        $this->checking = false;
    }

    /**
    * Check if a parameter exists
    * @param string $key Key to check if exists
    */
    public function has($key)
    {
        return isset($this->params[$key]);
    }

    /**
    * Get errors.
    * @return boolean
    */
    public function isValid()
    {
        // Return the errors array

        // return errors
        return empty($this->getErrors());
    }

    /**
    * Get errors.
    * @return array
    */
    public function getErrors()
    {
        // Return the errors array

        // return errors
        return $this->errors;
    }

    /**
    * Set params (e.g. POST params)
    * @param array $params Array of name/values
    */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
    * Tell validator which key to check in this chain. If the key is not found, an
    * error message will be created
    * @param string $message Our error message on fail
    * @return object Returns this to begin chaining.
    */
    public function check($key, $options=array())
    {
        // default options
        $options = array_merge(array(
            'optional' => false, // when true, only validate present
        ), $options);

        // if the key is not found, then create an error as we will assume then
        // this field is required
        if (!$this->has($key) and !$options['optional']) {

            // key is not found, and param is not optional
            $this->logError($key, $key . ' is missing');
            return $this;

        } elseif (!$this->has($key) and $options['optional']) {

            // key is not found, but param is optional so we can skip
            // turn off checking for this param
            $this->checking = false;

        } else {

            // key is found, so validate

            // set the value once
            $this->key = $key;
            $this->value = $this->params[$key];

            // open the log for errors
            $this->checking = true;

        }

        return $this;
    }


    /**
    * Value is not empty or whitespaces.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isNotEmpty($message)
    {

        // $reg = '/^$|\s+/'; // empty or white space

        // check
        if(empty($this->value) and $this->checking) { // match!
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Value is a valid email address.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isEmail($message)
    {

        if(! filter_var($this->value, FILTER_VALIDATE_EMAIL) and $this->checking) {
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Value is letters only, not numbers.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isLetters($message)
    {

        $reg = "/^[a-zA-Z\s]+$/"; // only letters

        if(! preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Value is a numbers. Positive, negative and zero accepted.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isNumeric($message)
    {

        $reg = "/^\d*\.?\d*$/"; // numeric

        if(! preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Value is only positive, no negative or zeros.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isPositiveNumber($message)
    {

        $reg = "/^[1-9][0-9]*$/"; // positive number expression

        if(! preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        }

        return $this;

    }

    /**
    * Value is not positive, negative or zeros ok.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isNotPositiveNumber($message)
    {

        $reg = "/^[1-9][0-9]*$/"; // positive number expression

        if(preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        }

        return $this;

    }

    /**
    * Value is only negative, no positive or zeros.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isNegativeNumber($message)
    {

        $reg = "/^-[1-9][0-9]*$/"; // negetive number expression

        if(! preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        }

        return $this;

    }

    /**
    * Value is not negative, positives and zeros ok.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isNotNegativeNumber($message)
    {

        $reg = "/^-[1-9][0-9]*$/"; // negetive number expression

        if(preg_match($reg, "{$this->value}") and $this->checking) { // match
            $this->logError($this->key, $message);
        }

        return $this;

    }

    /**
    * Value is valid date time string.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isDateTime($message)
    {

        $reg = "/^\d{4}-\d{2}-\d{2} ([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/"; // date time expression

        if(! preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        } else {

            // the date may have the correct format, but let's check for leap years, 30 day months etc
            $datetime = explode(' ', $this->value);
            $date = explode('-', $datetime[0]);

            $year = intval($date[0]);
            $month = intval($date[1]);
            $day = intval($date[2]);

            if(! checkdate($month, $day, $year) and $this->checking) {
                $this->logError($this->key, $message);
            }
        }

        return $this;
    }

    /**
    * Value is valid date string.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isDate($message)
    {

        $reg = "/^\d{4}-\d{2}-\d{2}$/"; // date expression

        if(! preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        } else {

            // the date may have the correct format, but let's check for leap years, 30 day months etc
            $date = explode('-', $this->value);

            $year = intval($date[0]);
            $month = intval($date[1]);
            $day = intval($date[2]);

            if(! checkdate($month, $day, $year) and $this->checking) {
                $this->logError($this->key, $message);
            }
        }

        return $this;
    }

    /**
    * Value is valid time string.
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isTime($message)
    {

        $reg = "/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/"; // time expression

        if(! preg_match($reg, "{$this->value}") and $this->checking) {
            $this->logError($this->key, $message);
        }

        return $this;
    }





    // the following are useful for checking passwords



    /**
    * Ensure meets minimum length of characters
    * @param string $message Our error message on fail
    * @param string $min Min legth of string
    * @return object Returns this to allow chaining.
    */
    public function isMinimumLength($min, $message)
    {
        // check
        if(strlen($this->value) < $min and $this->checking) { // match!
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Ensure meets maximum length
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function isMaximumLength($max, $message)
    {
        // check
        if(strlen($this->value) > $max and $this->checking) { // match!
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Ensure meets minimum length
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function hasUpperCase($message)
    {
        $reg = '/[A-Z]/'; // valid check

        // check
        if(!preg_match($reg, $this->value) and $this->checking) { // match!
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Ensure meets minimum length
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function hasLowerCase($message)
    {

        $reg = '/[a-z]/'; // valid check

        // check
        if(!preg_match($reg, $this->value) and $this->checking) { // match!
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
    * Ensure meets minimum length
    * @param string $message Our error message on fail
    * @return object Returns this to allow chaining.
    */
    public function hasNumber($message)
    {

        $reg = '/[0-9]/'; // valid check

        // check
        if(!preg_match($reg, $this->value) and $this->checking) { // match!
            $this->logError($this->key, $message);
        }

        return $this;
    }

    /**
     * Will compare two fields are the same (e.g. password, password_confirm)
     * @param string $compareKey The other value key to compare with
     * @param string $message Our error message on fail
     * @return Validator
     * TODO move to lib
     */
    public function isSameAs($compareKey, $message)
    {
        //check whether this email exists in the db
        if ($this->params[$this->key] != $this->params[$compareKey]) {
            $this->logError($this->key, $message);
        }

        // return instance
        return $this;
    }

}
