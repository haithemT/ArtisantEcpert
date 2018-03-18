<?php
namespace Poll\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Poll implements InputFilterAwareInterface
{
    public $id;
    public $question;
    public $event_id;
    public $status;
    public $last_updated;
    public $created;
    public $created_by;
    
    private $inputFilter;
    

    public function exchangeArray($data)
    {
        $this->id 		            = (isset($data['id'])) ? $data['id'] : null;
        $this->question             = (isset($data['question'])) ? $data['question'] : null;
        $this->event_id             = (isset($data['event_id'])) ? $data['event_id'] : null;
        $this->status               = (isset($data['status'])) ? $data['status'] : null;
        $this->last_updated         = (isset($data['last_updated'])) ? $data['last_updated'] : null;
        $this->created              = (isset($data['created'])) ? $data['created'] : null;
        $this->created_by           = (isset($data['created_by'])) ? $data['created_by'] : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'                    => $this->id,
            'question'              => $this->question,
            'event_id'              => $this->event_id,
            'status'                => $this->status,
            'last_updated'          => $this->last_updated,
            'created'               => $this->created,
            'created_by'            => $this->created_by,
        ];
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'question',
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
                        'max' => 200,
                    ],
                ],
            ],
        ]); 
        
        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}