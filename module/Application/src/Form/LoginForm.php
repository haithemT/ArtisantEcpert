<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Captcha\Figlet;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Validator\StringLength;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    public function __construct($name = null,$type)
    {
        parent::__construct($type);
        $this->setAttribute('class', 'form-horizontal');
        
        $this->addCommonFields();
        $this->addcommonFilters();
        
        switch($type){
            case 'login':
                $this->addLoginFields();
                $this->addLoginFilters();
            break;
            case 'signup':
                $this->addSignUpFields();
                $this->addSignUpFilters();
                $this->addPasswordResetFields();
                $this->addPasswordResetFilters();
            break;
            case 'PASSWORDRESET':
                $this->addPasswordResetFields();
                $this->addPasswordResetFilters();
            break;
            default :
                return false;
        }
    }
    
    /**
     *
     * Fields for User Sign up
     *
     */
     private function addCommonFields()
    {
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Sign In',
                'id'    => 'submitbutton',
            ],
        ]);
        $this->add([
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ]
        ]);
         
        $this->add([
            'name' => 'captcha',
            'type' => 'Zend\Form\Element\Captcha',
            'options' => [
                'captcha' => new \Zend\Captcha\Figlet([
                    'wordLen' => 3,
                ]),
            ],
        ]);
    }
    /**
     *
     * Fields for User Sign up
     *
     */
     private function addLoginFields()
    {
        $this->add([
            'name' => 'usernameOrEmail',
            'type' => 'text',
            'options' => [
                'label' => 'Username Or Email',
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
        $this->add([
            'name' => 'rememberme',
            'type' => 'checkbox',
            'options' => [
                'label' => 'Remember me?',
            ],            
        ]);
    }
    
    /**
     *
     * Fields for User Sign up
     *
     */
     private function addSignUpFields()
    {
         $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
        $this->add([
            'name' => 'passwordConfirm',
            'type' => 'password',
            'options' => [
                'label' => 'Confirm Password',
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
        
        $this->add([
            'name' => 'firstname',
            'type' => 'text',
            'options' => [
                'label' => 'First name',
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);   
        $this->add([
            'name' => 'lastname',
            'type' => 'text',
            'options' => [
                'label' => 'Last name',
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
        $this->add([
            'name' => 'registration_type',
            'type' => 'radio',
            'options' => [
                'value_options' => [
                    '1' => [
                            'label' => '<span></span> Client',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-success push-10-r'],
                            'disable_html_escape' => true,
                            'value' => '1'
                    ],
                    '2' =>  [
                            'label' => '<span></span> Artisant',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-success'],
                            'value' => '2'
                    ],
                ],
                'label' => 'You are?',
                'label_options' => [
                    'disable_html_escape' => true,
                ]
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
    }
    /**
     *
     * Fields for User Reset Password
     *
     */
     private function addPasswordResetFields()
    {
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Enter your email',
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
    }
        
    /**
     *
     * Input filters for User common fields
     *
     */
    private function addcommonFilters()
    {
        $this->getInputFilter()->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 8,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
    }
    
    /**
     *
     * Input filters for User Log In
     *
     */
    private function addLoginFilters()
    {
        $this->getInputFilter()->add([
            'name' => 'usernameOrEmail',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
        $this->getInputFilter()->add([
            'name' => 'rememberme',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => ['0', '1'],
                    ],
                ],
            ]
        ]);
    }
    
     /**
     *
     * Input filters for User SignUp
     *
     */
    private function addSignUpFilters()
    {
        $this->getInputFilter()->add([
            'name' => 'passwordConfirm',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 8,
                        'max' => 100,
                    ],
                ],
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'password', 
                    ],
                ],
            ],
        ]);
        $this->getInputFilter()->add([
            'name' => 'registration_type',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => ['1', '2'],
                    ],
                ],
            ]
        ]);
        $this->getInputFilter()->add([
            'name' => 'firstname',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
        $this->getInputFilter()->add([
            'name' => 'lastname',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
        $this->getInputFilter()->add([
            'name' => 'username',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
    }
     /**
     *
     * Input filters for User SignUp
     *
     */
    private function addPasswordResetFilters()
    {
        $this->getInputFilter()->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
            ],
        ]);
    }
}
