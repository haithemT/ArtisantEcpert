<?php

namespace User\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('user');
        
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
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
            'name' => 'firstname',
            'type' => 'text',
            'options' => [
                'label' => 'First name',
            ],
        ]);
        $this->add([
            'name' => 'lastname',
            'type' => 'text',
            'options' => [
                'label' => 'Last Name',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id'    => 'submitbutton',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Add',
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
}