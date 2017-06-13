<?php
namespace Offre\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Offre implements InputFilterAwareInterface
{
    public $id;
    public $intitule;
    public $intitule_devis;
    public $description;
    
    private $inputFilter;
    

    public function exchangeArray($data)
    {
        $this->id 					= (isset($data['id'])) ? $data['id'] : null;
        $this->intitule 				= (isset($data['intitule'])) ? $data['intitule'] : null;
        $this->intitule_devis                           = (isset($data['intitule_devis'])) ? $data['intitule_devis'] : null;
        $this->description                              = (isset($data['description'])) ? $data['description'] : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'                        =>$this->id,
            'intitule'                  =>$this->intitule,
            'intitule_devis'            =>$this->intitule_devis,
            'description'               =>$this->description,
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
            'name' => 'intitule',
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