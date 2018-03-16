<?php
namespace Event\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Event implements InputFilterAwareInterface
{
    public $id;
    public $organizer_contact;
    public $organizer;
    public $status;
    public $eventName;
    public $description;
    public $startDate;
    public $endDate;
    public $facebook;
    public $twitter;
    public $twitterHashTag;
    public $instagram;
    public $country;
    public $city;
    public $address;
    public $picture;
    public $last_updated;
    public $created;
    public $creator;
    
    private $inputFilter;
    

    public function exchangeArray($data)
    {
        $this->id 		            = (isset($data['id'])) ? $data['id'] : null;
        $this->organizer_contact    = (isset($data['organizer_contact'])) ? $data['organizer_contact'] : null;
        $this->organizer            = (isset($data['organizer'])) ? $data['organizer'] : null;
        $this->status               = (isset($data['status'])) ? $data['status'] : null;
        $this->eventName            = (isset($data['eventName'])) ? $data['eventName'] : null;
        $this->description 	        = (isset($data['description'])) ? $data['description'] : null;
        $this->startDate            = (isset($data['startDate'])) ? $data['startDate'] : null;
        $this->endDate              = (isset($data['endDate'])) ? $data['endDate'] : null;      
        $this->facebook             = (isset($data['facebook'])) ? $data['facebook'] : null;
        $this->twitter              = (isset($data['twitter'])) ? $data['twitter'] : null;
        $this->twitterHashTag       = (isset($data['twitterHashTag'])) ? $data['twitterHashTag'] : null;
        $this->instagram            = (isset($data['instagram'])) ? $data['instagram'] : null;
        $this->country              = (isset($data['country'])) ? $data['country'] : null;
        $this->city                 = (isset($data['city'])) ? $data['city'] : null;
        $this->address              = (isset($data['address'])) ? $data['address'] : null;
        $this->picture              = (isset($data['picture'])) ? is_array($data['picture']) && !empty($data['picture']) ? $data['picture']['name'] : $data['picture']  : null;
        $this->last_updated         = (isset($data['last_updated'])) ? $data['last_updated'] : null;
        $this->created              = (isset($data['created'])) ? $data['created'] : null;
        $this->created_by           = (isset($data['created_by'])) ? $data['created_by'] : null;
        $this->updated_by           = (isset($data['created_by'])) ? $data['updated_by'] : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'                    => $this->id,
            'organizer_contact'     => $this->organizer_contact,
            'organizer'             => $this->organizer,
            'status'                => $this->status,
            'eventName'             => $this->eventName,
            'description'           => $this->description,
            'startDate'             => $this->startDate, 
            'endDate'               => $this->endDate,
            'facebook'              => $this->facebook,
            'twitter'               => $this->twitter,
            'twitterHashTag'        => $this->twitterHashTag,
            'instagram'             => $this->instagram,
            'country'               => $this->country,
            'city'                  => $this->city,
            'address'               => $this->address,
            'last_updated'          => $this->last_updated,
            'created'               => $this->created,
            'picture'               => $this->picture,
            'created_by'            => $this->created_by,
            'updated_by'            => $this->updated_by,
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
            'name' => 'eventName',
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
            'name' => 'organizer',
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
            'name' => 'description',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
        ]);

        // Add validation rules for the "file" field.	 
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'picture',
            'required' => true,   
            'validators' => [
                ['name'    => 'FileUploadFile'],
                [
                    'name'    => 'FileMimeType',                        
                    'options' => [                            
                        'mimeType'  => ['image/jpeg', 'image/png']
                    ]
                ],
                ['name'    => 'FileIsImage'],
                [
                    'name'    => 'FileImageSize',
                    'options' => [
                        'minWidth'  => 128,
                        'minHeight' => 128,
                        'maxWidth'  => 1024,
                        'maxHeight' => 1024
                    ]
                ],
            ],
            'filters'  => [                    
                [
                    'name' => 'FileRenameUpload',
                    'options' => [  
                        'target' => './data/upload',
                        'useUploadName' => true,
                        'useUploadExtension' => true,
                        'overwrite' => true,
                        'randomize' => false
                    ]
                ]
            ],   
        ]);                
        
        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}