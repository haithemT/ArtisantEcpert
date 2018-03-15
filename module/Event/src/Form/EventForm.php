<?php

namespace Event\Form;

use Zend\Form\Form;


class EventForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('post');
        $this->setAttribute('class', 'form-horizontal push-10-t');
        
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'eventName',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Event name',
                
            ],
        ]);
        $this->add([
            'name' => 'organizer',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Organizer',
                
            ],
        ]);
        $this->add([
            'name' => 'organizer_contact',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Organizer contact',
                
            ],
        ]);
        $this->add([
            'name' => 'description',
            'type' => 'textarea',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Description',
            ],
        ]);
        $this->add([
            'name' => 'startDate',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Start date',
            ],
        ]);
        $this->add([
            'name' => 'endDate',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'End date',
            ],
        ]);
        $this->add([
            'name' => 'facebook',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'faceBook page',
            ],
        ]);
        $this->add([
            'name' => 'twitter',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Twitter page',
            ],
        ]);
        $this->add([
            'name' => 'twitterHashTag',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Twitter #',
            ],
        ]);
        $this->add([
            'name' => 'instagram',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Instagram',
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
            'name' => 'country',
            'type' => 'select',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>'Country',
                'value_options' =>[
                    '1'  => 'Tunisia',
                    '2'  => 'France',
                    '3'  => 'UK'
                ]
            ],
        ]);
        $this->add([
            'name' => 'city',
            'type' => 'select',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' =>'City',
                'value_options' =>[
                    '1'     => 'Tunis',
                    '2'     => 'Paris',
                    '3'     => 'Londeon'
                ]
            ],
        ]);
        $this->add([
            'name' => 'address',
            'type' => 'text',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Address',
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
            'name' => 'picture',
            'type' => 'file',
            'attributes' => [
                'class' => 'form-control',
                'id'    => 'picture'
            ],
            'options' => [
                'label' => 'Logo',
            ],
        ]);
    }
}