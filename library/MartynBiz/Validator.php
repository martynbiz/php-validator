<?php

namespace MartynBiz;

/**
* 
* Designed for string validation (e.g. form submissions). Allows a single value to be checked through a series of conditions through method chaining.
*
* @category ChainValidator
* @package Validator
* @author Martyn Bissett <martynbissett@yahoo.co.uk>
* @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
* @version 1.0
**/
class Validator {
	
	/**
	* Will contain the errors for the duration of the objects existence
	*
	* @var errors
	*/
	private $_errors = array();
	
	/**
	* Value of the variable being validated, set in chain()
	*
	* @var errors
	*/
	private $_value = array();
	
	/**
	* When an erro is found in a chain, switch of error logging for the remainder of the chain
	*
	* @var errors
	*/
	private $_logOpen = array();
    
    // PUBLIC FUNCTIONS
    
    /**
	* Simply return the instantiated object, or instantiated if not already done so.
	*
	* @return object Returns the current instance.
	*/
    public function getInstance() {
		
		static $obj_instance;
	
		if (!isset ($obj_instance)) {
		    $obj_instance = new ChainValidator();
		}
	
		return $obj_instance;
		
    }
	
	// other functions
	
	/**
	* Log an error.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function logError($error_message, $error_code=null) {
		// Log a new error
		// error_message - this is the error message, this is custom
		// error_code - this is the error code that we will affix to this error
		
		// build the error array to push into the errors variable
		$error = array(
			'message' => $error_message
			);
		if($error_code) $error['code'] = $error_code;
		
		array_push($this->_errors, $error);
		
		// we have tracked a log, close the log for the rest of the chain
		$this->_logOpen = false;
	}
	
	/**
	* Get errors.
	*
	* @return array Returns errors.
	*/
	public function getErrors() {
		// Return the errors array
		
		// return errors
		return $this->_errors;
	}
	
	/**
	* Has errors.
	*
	* @return array Returns errors.
	*/
	public function hasErrors() {
		// Return the errors array
		
		// return errors
		return (count($this->getErrors()) > 0);
	}
	
	/**
	* Start a chain.
	* 
	* @param string $value set the value we are going to validate in this chain
	*
	* @return object Returns this to begin chaining.
	*/
	public function set($value) {
		// Return the errors array
		
		// set the value once
		$this->_value = $value;
		
		// open the log for errors
		$this->_logOpen = true;
		
		return $this;
	}
	
	
	/**
	* Value is not empty or whitespaces.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isNotEmpty($error_message, $error_code=null) {
		
		$reg = "'^[\s]*[\s]*$'"; // empty or white space
		
		if(preg_match($reg, "{$this->_value}") and $this->_logOpen) { // match!
			$this->logError($error_message, $error_code);
		}
		
		return $this;
	}
	
	/**
	* Value is a valid email address.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isEmail($error_message, $error_code=null) {
		
		$reg = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/"; // email address
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		}
		
		return $this;
	}
	
	/**
	* Value is letters only, not numbers.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isLetters($error_message, $error_code=null) {
		
		$reg = "/^[a-zA-Z\s]+$/"; // only letters
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		}
		
		return $this;
	}
	
	/**
	* Value is a numbers. Positive, negative and zero accepted.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isNumeric($error_message, $error_code=null) {
		
		$reg = "/^\d*\.?\d*$/"; // numeric
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		}
		
		return $this;
	}
	
	/**
	* Value is only positive, no negative or zeros.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isPositiveNumber($error_message, $error_code=null) {
		
		$reg = "/^[1-9][0-9]*$/"; // positive number expression
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		}
		
		return $this;
		
	}
	
	/**
	* Value is not positive, negative or zeros ok.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isNotPositiveNumber($error_message, $error_code=null) {
		
		$reg = "/^[1-9][0-9]*$/"; // positive number expression
		
		if(preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		}
		
		return $this;
		
	}
	
	/**
	* Value is only negative, no positive or zeros.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isNegativeNumber($error_message, $error_code=null) {
		
		$reg = "/^-[1-9][0-9]*$/"; // negetive number expression
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		}
		
		return $this;
		
	}
	
	/**
	* Value is not negative, positives and zeros ok.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isNotNegativeNumber($error_message, $error_code=null) {
		
		$reg = "/^-[1-9][0-9]*$/"; // negetive number expression
		
		if(preg_match($reg, "{$this->_value}") and $this->_logOpen) { // match
			$this->logError($error_message, $error_code);
		}
		
		return $this;
		
	}
	
	/**
	* Value is valid date time string.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isDateTime($error_message, $error_code=null) {
		
		$reg = "/^\d{4}-\d{2}-\d{2} ([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/"; // date time expression
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		} else {
			
			// the date may have the correct format, but let's check for leap years, 30 day months etc
			$datetime = explode(' ', $this->_value);
			$date = explode('-', $datetime[0]);
			
			$year = intval($date[0]);
			$month = intval($date[1]);
			$day = intval($date[2]);
			
			if(! checkdate($month, $day, $year) and $this->_logOpen) {
				$this->logError($error_message, $error_code);
			}
		}
		
		return $this;
	}

	/**
	* Value is valid date string.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isDate($error_message, $error_code=null) {
		
		$reg = "/^\d{4}-\d{2}-\d{2}$/"; // date expression
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		} else {
			
			// the date may have the correct format, but let's check for leap years, 30 day months etc
			$date = explode('-', $this->_value);
			
			$year = intval($date[0]);
			$month = intval($date[1]);
			$day = intval($date[2]);
			
			if(! checkdate($month, $day, $year) and $this->_logOpen) {
				$this->logError($error_message, $error_code);
			}
		}
		
		return $this;
	}
	
	/**
	* Value is valid time string.
	* 
	* @param string $error_message human readable error message
	* @param integer $error_code (optional) can set a numeric value for this type of error
	*
	* @return object Returns this to allow chaining.
	*/
	public function isTime($error_message, $error_code=null) {
		
		$reg = "/^([01]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/"; // time expression
		
		if(! preg_match($reg, "{$this->_value}") and $this->_logOpen) {
			$this->logError($error_message, $error_code);
		}
		
		return $this;
	}
	
}

?>
