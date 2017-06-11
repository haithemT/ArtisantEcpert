<?php

namespace User\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('user');
        $this->setAttribute('class', 'form-horizontal push-10-t');
        
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Username',
                
            ],
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'email',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Email',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name' => 'firstname',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'First name',
            ],
        ]);
        $this->add([
            'name' => 'lastname',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Last Name',
            ],
        ]);
        $this->add([
            'name' => 'facebook',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Facebook',
            ],
        ]);
        $this->add([
            'name' => 'linkedin',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'LinkedIn',
            ],
        ]);
        
        $this->add([
            'name' => 'avatar',
            'type' => 'file',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Avatar',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value'     => 'Add new user',
                'id'        => 'submitbutton',
                'class'     => 'btn btn-primary'
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
            'name' => 'role',
            'type' => 'radio',
            'options' => [
                'value_options' => [
                    '1' => [
                            'label' => '<span></span> Client',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-primary push-10-r'],
                            'disable_html_escape' => true,
                            'value' => '1'
                    ],
                    '2' =>  [
                            'label' => '<span></span> Artisant',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-primary push-10-r'],
                            'value' => '2'
                    ],
                    '3' =>  [
                            'label' => '<span></span> Admin',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-primary'],
                            'value' => '3'
                    ],
                ],
                'label' => 'Role',
                'label_options' => [
                    'disable_html_escape' => true,
                ]
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
        
        $this->add([
            'name' => 'locked',
            'type' => 'radio',
            'options' => [
                'value_options' => [
                    '1' => [
                            'label' => '<span></span> Yes',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-primary push-10-r'],
                            'disable_html_escape' => true,
                            'value' => '1'
                    ],
                    '0' =>  [
                            'label' => '<span></span> No',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-primary push-10-r'],
                            'value' => '0'
                    ]
                ],
                'label' => 'Lock account',
                'label_options' => [
                    'disable_html_escape' => true,
                ]
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
        
         $this->add([
            'name' => 'enabled',
            'type' => 'radio',
            'options' => [
                'value_options' => [
                    '1' => [
                            'label' => '<span></span> Yes',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-primary push-10-r'],
                            'disable_html_escape' => true,
                            'value' => '1'
                    ],
                    '0' =>  [
                            'label' => '<span></span> No',
                            'label_attributes' => ['class' => 'css-input css-radio css-radio-primary push-10-r'],
                            'value' => '0'
                    ]
                ],
                'label' => 'Lock account',
                'label_options' => [
                    'disable_html_escape' => true,
                ]
            ],
            'attributes' => [
                'class'=> 'form-control'
            ]
        ]);
    }
}