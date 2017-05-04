<?php

use MartynBiz\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new Validator();
    }

    public function testInitialization()
    {
        $validator = $this->validator;

        $this->assertTrue($validator instanceof Validator); // yey!
    }

    public function testLogErrors()
    {
        $validator = $this->validator;

        // assert errors is empty initially
        $this->assertEquals(0, count($validator->getErrors()));

        $message = 'I logged this';
        $key = 'my_key';
        $validator->logError($key, $message);

        // get errors
        $errors = $validator->getErrors();

        // // check it has something now
        $this->assertEquals(1, count($errors));
        $this->assertTrue( array_key_exists($key, $errors) );
        $this->assertEquals($message, current($errors));
    }

    public function testHas()
    {
        $validator = $this->validator;
        $validator->setData([
            'name' => '', // empty should return true
            'age' => 34, // numbers should return true
            'email' => 'martyn@example.com',
        ]);

        $this->assertTrue($validator->has('name'));
        $this->assertTrue($validator->has('age'));
        $this->assertTrue($validator->has('email'));
        $this->assertFalse($validator->has('missing_field'));
    }

    public function testCheckWhenKeyNotInParams()
    {
        $validator = $this->validator;
        $validator->setData(array(
            'name' => '', // empty should return true
            'age' => 34, // numbers should return true
            'email' => 'martyn@example.com',
        ));

        // key is found
        $result = $validator->check('name');
        $result = $validator->check('age');
        $result = $validator->check('email');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertTrue($validator->isValid());
        $this->assertEquals(0, count($errors));

        // check missing key
        $result = $validator->check('missing_key');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertFalse($validator->isValid());
        $this->assertTrue( array_key_exists('missing_key', $errors) );
        $this->assertEquals(1, count($errors));
    }

    public function testOptionalWhenKeyNotInParams()
    {
        $validator = $this->validator;
        $validator->setData(array(
            'name' => '',
            // 'age' => '', // missing
        ));

        $validator->check('name', array('optional' => true))
            ->isNotEmpty('Name cannot be empty');

        $validator->check('age', array('optional' => true))
            ->isNotEmpty('Name cannot be empty');

        // get errors. we expect only one error though, the param that
        // was given (name)
        $errors = $validator->getErrors();

        // assert
        $this->assertTrue( array_key_exists('name', $errors) );
        $this->assertFalse( array_key_exists('age', $errors) );
    }

    /**
     * @dataProvider getIsNotEmptyArray
     */
    public function testErrorMessageIsSet($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData([
            'name' => ''
        ]);

        // validate
        $message = 'Value is empty';
        $result = $validator->check('name')
            ->isNotEmpty($message);

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertFalse($validator->isValid());
        $this->assertTrue( array_key_exists('name', $errors) );
        $this->assertEquals($message, $errors['name']);
    }

    /**
     * @dataProvider getIsNotEmptyArray
     */
    public function testCheckingStopsInChainWhenErrorFound($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData([
            'email' => ''
        ]);

        // validate
        $messageEmpty = 'Value is empty'; // should stop at this error
        $messageNotEmail = 'Value is not valid email';
        $result = $validator->check('email')
            ->isNotEmpty($messageEmpty)
            ->isEmail($messageNotEmail);

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertFalse( $validator->isValid() );
        $this->assertTrue( array_key_exists('email', $errors) );
        $this->assertEquals($messageEmpty, $errors['email']);
    }

    public function testCheckAloneSetsErrorWhenParamMissing()
    {
        $validator = $this->validator;
        $validator->setData(array(
            'name' => 'Martyn',
        ));

        // validate
        $result = $validator->check('agreement');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals(false, $validator->isValid());
        $this->assertEquals(1, count($errors));
    }

    /**
     * @dataProvider getIsNotEmptyArray
     */
    public function testIsNotEmpty($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('name')
            ->isNotEmpty('Missing field');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }

    /**
     * @dataProvider getIsEmptyArray
     */
    public function testIsEmpty($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('name')
            ->isEmpty('Missing field');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }

    /**
     * @dataProvider getIsEmailArray
     */
    public function testIsEmail($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('email')
            ->isEmail('Not email');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }

    /**
     * @dataProvider getIsMinimumLengthArray
     */
    public function testIsMimimumLength($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('password')
            ->isMinimumLength(8, 'Not long enough');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }

    /**
     * @dataProvider getIsMaximumLengthArray
     */
    public function testIsMaximumLength($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('password')
            ->isMaximumLength(8, 'To long');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }

    /**
     * @dataProvider getHasUpperCaseArray
     */
    public function testHasUpperCase($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('password')
            ->hasUpperCase('No upper case');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }

    /**
     * @dataProvider getHasLowerCaseArray
     */
    public function testHasLowerCase($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('password')
            ->hasLowerCase('No lower case');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }

    /**
     * @dataProvider getHasNumberArray
     */
    public function testHasNumber($data, $valid)
    {
        $validator = $this->validator;
        $validator->setData($data);

        // validate
        $result = $validator->check('password')
            ->hasNumber('No number');

        // get errors
        $errors = $validator->getErrors();

        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }


    // data providers

    public function getIsEmptyArray()
    {
        return [
            [
                [
                    'name' => ''
                ],
                true
            ],
            [
                [
                    'name' => null
                ],
                true
            ],
            [
                [
                    'name' => false
                ],
                true
            ],

            [
                [
                    'name' => 'something'
                ],
                false
            ],
        ];
    }

    public function getIsNotEmptyArray()
    {
        return [
            [
                [
                    'name' => ''
                ],
                false
            ],
            // [
            //     [
            //         'name' => null
            //     ],
            //     false
            // ],
            // [
            //     [
            //         'name' => false
            //     ],
            //     false
            // ],

            [
                [
                    'name' => 'something'
                ],
                true
            ],
            [
                [
                    'name' => '0'
                ],
                true
            ],
        ];
    }

    public function getIsEmailArray()
    {
        return [
            [
                [
                    'email' => 'martyn'
                ],
                false
            ],
            [
                [
                    'email' => 'martyn@',
                ],
                false
            ],
            [
                [
                    'email' => 'martyn@yahoo',
                ],
                false
            ],
            [
                [
                    'email' => 'martyn@yahoo.',
                ],
                false
            ],
            [
                [
                    'email' => 'yahoo.com',
                ],
                false
            ],
            [
                [
                    'email' => '@yahoo.com',
                ],
                false
            ],

            [
                [
                    'email' => 'martyn@yahoo.com',
                ],
                true
            ],
            [
                [
                    'email' => 'martyn+something@yahoo.com',
                ],
                true
            ],
        ];
    }

    public function getIsMinimumLengthArray()
    {
        return [
            [
                [
                    'password' => '1'
                ],
                false
            ],
            [
                [
                    'password' => '1234567'
                ],
                false
            ],
            [
                [
                    'password' => '12345678'
                ],
                true
            ],
            [
                [
                    'password' => '12345678901234'
                ],
                true
            ],
        ];
    }

    public function getIsMaximumLengthArray()
    {
        return [
            [
                ['password' => '1'], true
            ],
            [
                ['password' => '1234567'], true
            ],
            [
                ['password' => '12345678'], true
            ],
            [
                ['password' => '12345678901234'], false
            ],
        ];
    }

    public function getHasUpperCaseArray()
    {
        return [
            [
                ['password' => 'abc'], false
            ],
            [
                ['password' => 'Abc'], true
            ],
            [
                ['password' => 'ABc'], true
            ],
        ];
    }

    public function getHasLowerCaseArray()
    {
        return [
            [
                ['password' => 'ABC'], false
            ],
            [
                ['password' => 'aBC'], true
            ],
            [
                ['password' => 'abC'], true
            ],
        ];
    }

    public function getHasNumberArray()
    {
        return [
            [
                ['password' => 'abc'], false
            ],
            [
                ['password' => '1bc'], true
            ],
            [
                ['password' => '12c'], true
            ],
        ];
    }
}
