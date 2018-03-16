<?php

namespace Feed\Form;

use Zend\Form\Form;


class CommentForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('comment');
        $this->setAttribute('class', 'form-horizontal push-10-t');
        
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'comment',
            'type' => 'textarea',
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Comment',
                
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
        // $this->add([
        //     'name' => 'picture',
        //     'type' => 'file',
        //     'attributes' => [
        //         'class' => 'form-control'
        //     ],
        //     'options' => [
        //         'label' => 'Image/Video',
        //     ],
        // ]);
    }
}