##Installation

Install with composer:

```
composer require martynbiz/php-validator
```

After that, create a new instance of the class and set the params to check (e.g. $_POST).

```php
$validator = new MartynBiz\Validator();
$validator->setParams($_POST);
```

### Basic Usage

The set() method instructs the object that this is a new value to validate. Additional methods are chained from this. Below shows an example of the object checking a value is not empty, then checking it is a valid email address.

```php
$email = '';
$validator->check('email')
  ->isNotEmpty('Email address is blank')
  ->isEmail('Invalid email address');
```

To fetch the errors from a validation check, use getErrors():

```php
$errors = $validator->getErrors();
```

After validation, check if params are valid:

```php
$continue = $validator->isValid();
```

Check if a params exists in the validator (basically just isset, but makes code more readable):

```php
if ($validator->has('name')) {...
```

This will return an array of containing the error that occured (Email address is blank). Please note that although the isEmail method was called too, because it has already gathered an error it does not record another. Remember this when ordering your methods in the chain.

It is also possible to do this in one go, and return errors:

```php
$errors = $validator->check('email')
  ->isNotEmpty('Email address is blank')
  ->isEmail('Invalid email address')
  ->getErrors();
```

Optional fields are when validation only occurs on that param if the param is given.

```php
$validator->check('age', array('optional' => true))
  ->is...
```

### Other methods for validating

The above example shows how to validate an email address but the following methods can be used to numeric, date and time stings too. Below is the full list of validation methods available.

```php
// strings

$validator->check('name')
  ->isNotEmpty('Value must not be blank');

$validator->check('email')
  ->isEmail('Email address must be valid');

$validator->check('name')
  ->isLetters('Value must be letters');

// numeric strings

$validator->check('amount')
  ->isNumber('Value must be a number');

$validator->check('profit')
  ->isPositiveNumber('Value must be a positive number');

$validator->check('loss')
  ->isNotPositiveNumber('Value must not be a positive number, negatives and zeros OK');

$validator->check('loss')
  ->isNegativeNumber('Value must be a negative number');

$validator->check('profit')
  ->isNotNegativeNumber('Value must not be a negative number, positives and zeros OK');

// date/time

$validator->check('publish_date')
  ->isDateTime('Value must be date/time format yyyy-mm-dd hh:mm:ss');

$validator->check('publish_date')
  ->isDate('Value must be date format yyyy-mm-dd');

$validator->check('meeting_time')
  ->isTime('Value must be time hh:mm:ss');

// passwords

$message = 'Password must contain upper and lower case characters, and have more than 8 characters';
$validator->check('password')
  ->isNotEmpty($message)
  ->hasLowerCase($message)
  ->hasUpperCase($message)
  ->hasNumber()
  ->isMinimumLength($message, 8);

// etc

$validator->check('username')
  ->isNotEmpty('Username missing')
  ->isMaximumLength('Username must not exceed 16 characters', 16);
```

### Other error logging ###

You can use the logError() method too to log a custom error:

```php
$validator->logError('Could not load api', 5000);
```

### Extend Validator ###

Sometimes it's useful to add your own validator methods. This can be done by extending the class, and
ensuring that your new method returns the instance logs the error:

```php
class MyValidator extends Validator
{
  public function isUniqueEmail($message)
  {
    //check whether this email exists in the db
    //this is an example model, use your own models here
    $user = $myUsersModel->findByEmail( $this->value );

    // log error - required
    if ($user) {
      $this->logError($message);
    }

    // return instance - required
    return $this;
  }
}
```

Then you can chain the method as with the built in ones:

```php
$validator = new MyValidator():
$validator->setParams($_POST);
$validator->check('email')
  ->isNotEmpty()
  ->isEmail()
  ->isUniqueEmail(); // new method
```

#TODO#

* finish tests
* pass params like $validator = new MartynBiz\Validator($params);
* $validator->check('name')->isRequired() .. OR $validator->optional('name')->isNotEmpty(), $validator->required('name')->isNotEmpty()
