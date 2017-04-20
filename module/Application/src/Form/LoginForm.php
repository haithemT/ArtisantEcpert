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
    public function __construct($name = null)
    {
        parent::__construct('login');
        $this->add([
            'name' => 'usernameOrEmail',
            'type' => 'text',
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name' => 'rememberme',
            'type' => 'checkbox',
            'options' => [
                'label' => 'Remember me?',
            ],            
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
        
        /**
         * Apply login filter
         */
        $this->addLoginFilters();
    }
    
    /**
     *
     * Fields for User Log In
     *
     */
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
            'validators' => array(
                array(
                    'name' => 'InArray',
                    'options' => array(
                        'haystack' => array('0', '1'),
                     ),
                ),
            )
        ]);
    }
}
