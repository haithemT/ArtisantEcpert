<?php
namespace Blog\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Post implements InputFilterAwareInterface
{
    public $id;
    public $author_id;
    public $author;
    public $status;
    public $title;
    public $content;
    public $excerpt;
    public $post_date;
    public $updated;
    
    private $inputFilter;
    

    public function exchangeArray($data)
    {
        $this->id 					= (isset($data['id'])) ? $data['id'] : null;
        $this->author_id 				= (isset($data['author_id'])) ? $data['author_id'] : null;
        $this->status                                   = (isset($data['status'])) ? $data['status'] : null;
        $this->title                                    = (isset($data['title'])) ? $data['title'] : null;
        $this->content 					= (isset($data['content'])) ? $data['content'] : null;
        $this->excerpt                                  = (isset($data['excerpt'])) ? $data['excerpt'] : null;
        $this->post_date                                = (isset($data['post_date'])) ? $data['post_date'] : null;      
        $this->updated                                  = (isset($data['updated'])) ? $data['updated'] : null;
        $this->author                                   = (isset($data['firstname']) && isset($data['lastname'])) ? $data['firstname'].' '.$data['lastname'] : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'                    =>$this->id,
            'author_id'             =>$this->author_id,
            'status'                =>$this->status,
            'title'                 =>$this->title,
            'content'               =>$this->content,
            'excerpt'               =>$this->excerpt, 
            'post_date'             =>$this->post_date,
            'updated'               =>$this->updated,
            'author'                =>$this->author
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
            'name' => 'title',
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

        $inputFilter->add([
            'name' => 'content',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
        
        $inputFilter->add([
            'name' => 'excerpt',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);
        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}