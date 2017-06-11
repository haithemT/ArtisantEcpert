<?php

namespace User\Form;

use Zend\Form\Form;


class FeedbackForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('feedback');
        $this->setAttribute('class', 'form-horizontal push-10-t');
        
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'text',
            'type' => 'textarea',
            'attributes' => [
                'class' => 'form-control',
                'rows'  => 10
            ],
            'options' => [
                'label' => 'Your feedback',
                
            ],
        ]);
        $this->add([
            'name' => 'rate',
            'type' => 'hidden',
            'attributes' => [
                'class' => 'form-control',
                'value' => ''
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value'     => 'Add feedback',
                'id'        => 'submitbutton',
                'class'     => 'btn btn-primary'
            ],
        ]);
        $this->add([
            'name' => 'highlight', 
            'type' => 'Zend\Form\Element\Radio', 
            'attributes' => [
                'value' => '1',
            ], 
            'options' => [
                'label' => 'Highlight',
                'label_attributes' => [
                    'class' => '',
                ],
                'value_options' => [
                    '0' => 'No',
                    '1' => 'Yes',
                ],
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
    }
}