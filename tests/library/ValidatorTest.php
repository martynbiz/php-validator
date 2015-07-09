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
        $this->assertTrue($this->validator instanceof Validator); // yey!
    }
    
    /**
     * @dataProvider getIsNotEmptyArray
     */
    public function testErrorMessageIsSet($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams([
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
    public function testIsNotEmpty($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('name')
            ->isNotEmpty();
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    /**
     * @dataProvider getIsEmailArray
     */
    public function testIsEmail($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('email')
            ->isEmail();
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    /**
     * @dataProvider getIsMinimumLengthArray
     */
    public function testIsMimimumLength($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('password')
            ->isMinimumLength(8);
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    /**
     * @dataProvider getIsMaximumLengthArray
     */
    public function testIsMaximumLength($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('password')
            ->isMaximumLength(8);
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    /**
     * @dataProvider getIsLengthWithinArray
     * @depends testIsMimimumLength
     * @depends testIsMaximumLength
     */
    public function testIsLengthWithin($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('password')
            ->isLengthWithin(4, 8);
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    /**
     * @dataProvider getHasUpperCaseArray
     */
    public function testHasUpperCase($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('password')
            ->hasUpperCase();
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    /**
     * @dataProvider getHasLowerCaseArray
     */
    public function testHasLowerCase($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('password')
            ->hasLowerCase();
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    /**
     * @dataProvider getHasNumberArray
     */
    public function testHasNumber($params, $valid)
    {
        $validator = $this->validator;
        $validator->setParams($params);
        
        // validate
        $result = $validator->check('password')
            ->hasNumber();
        
        // get errors
        $errors = $validator->getErrors();
        
        // assert
        $this->assertEquals($valid, $validator->isValid());
        $this->assertEquals(($valid ? 0 : 1), count($errors));
    }
    
    
    // data providers
    
    public function getIsNotEmptyArray()
    {
        return [
            [
                [
                    'name' => ''
                ], 
                false
            ],
            [
                [
                    'name' => null
                ], 
                false
            ],
            [
                [
                    'name' => false
                ], 
                false
            ],
            
            [
                [
                    'name' => 'something'
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
    
    public function getIsLengthWithinArray()
    {
        return [
            [
                ['password' => '1'], false
            ],
            [
                ['password' => '123'], false
            ],
            [
                ['password' => '1234'], true
            ],
            [
                ['password' => '1234567'], true
            ],
            [
                ['password' => '12345678'], true
            ],
            [
                ['password' => '123456789'], false
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