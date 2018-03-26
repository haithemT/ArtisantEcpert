<?php

namespace Poll\Form;

use Zend\Form\Form;
use Zend\Form\Element;


class PollForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('poll');
        $this->setAttribute('class', 'form-horizontal push-10-t');
        
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'question',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Question',
                
            ],
        ]);
        $this->add([
            'type' => Element\Collection::class,
            'name' => 'responses',
            'options' => [
                'label' => 'Response',
                'count' => 2,
                // Do not allow adding:
                'allow_add' => true,
                // Do not display the index template:
                'should_create_template' => false,
                'target_element' => [
                    'type' => 'text', // CategoryFieldset::class,
                ],
            ],
        ]);
        $this->add([
            'name' => 'question',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Question',
                
            ],
        ]);

        $this->add([
            'name' => 'status',
            'type' => 'select',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>'Status',
                'value_options' =>[
                'active'        => 'Active',
                'archived'      => 'Archived',
                'scheduled'     => 'Scheduled'
            ]
            ],
        ]);
        $this->add([
            'name' => 'event',
            'type' => 'select',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>'Events',
                'value_options' =>[]
            ],
        ]);
       
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value'     => 'Add new poll',
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
    }
}