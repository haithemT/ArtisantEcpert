<?php
namespace User\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Feedback implements InputFilterAwareInterface
{
    public $id;
    public $user_id;
    public $text;
    public $rate;
    public $date;
    public $highlight;
    public $user_full_name;
    
    private $inputFilter;
    

    public function exchangeArray($data)
    {
        $this->id 					= (isset($data['id'])) ? $data['id'] : null;
        $this->user_id                                  = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->text                                     = (isset($data['text'])) ? $data['text'] : null;
        $this->rate                                     = (isset($data['rate'])) ? $data['rate'] : null;
        $this->date                                     = (isset($data['date'])) ? $data['date'] : null;
        $this->highlight 				= (isset($data['highlight'])) ? $data['highlight'] : null;
        $this->user_full_name 				= (isset($data['firstname']) && isset($data['lastname'])) ? $data['firstname'].' '.$data['lastname'] : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'                =>$this->id,
            'user_id'           =>$this->user_id,
            'text'              =>$this->text,
            'rate'              =>$this->rate,
            'date'              =>$this->date,
            'highlight'         =>$this->highlight,
            'user_full_name'    =>$this->user_full_name,
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
            'name' => 'text',
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
                        'max' => 1000,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name' => 'rate',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name' => \Zend\Validator\Between::class,
                    'options' => [
                        'min' => 1,
                        'max' => 5,
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}