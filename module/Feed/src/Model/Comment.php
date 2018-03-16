<?php
namespace Feed\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Comment implements InputFilterAwareInterface
{
    public $id;
    public $author_id;
    public $post_id;
    public $comment;
    public $ip;
    public $approved;
    public $created;
    public $updated;
    
    private $inputFilter;
    

    public function exchangeArray($data)
    {
        $this->id 		    = (isset($data['id'])) ? $data['id'] : null;
        $this->author_id 	= (isset($data['author_id'])) ? $data['author_id'] : null;
        $this->author       = (isset($data['firstname']) && isset($data['lastname'])) ? $data['firstname'].' '.$data['lastname'] : null;
        $this->post_id      = (isset($data['post_id'])) ? $data['post_id'] : null;
        $this->comment      = (isset($data['comment'])) ? $data['comment'] : null;
        $this->ip 		    = (isset($data['ip'])) ? $data['ip'] : null;   
        $this->approved     = (isset($data['approved'])) ? $data['approved'] : null;
        $this->created      = (isset($data['created'])) ? $data['created'] : null;
        $this->updated      = (isset($data['updated'])) ? $data['updated'] : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'         => $this->id,
            'author_id'  => $this->author_id,
            'author'     => $this->author,
            'post_id'    => $this->post_id,
            'comment'    => $this->comment,
            'ip'         => $this->ip,
            'approved'   => $this->approved,
            'updated'    => $this->updated,
            'created'    => $this->created,
            'updated'    => $this->updated
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
            'name' => 'comment',
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