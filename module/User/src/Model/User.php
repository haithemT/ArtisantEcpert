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

class User implements InputFilterAwareInterface
{
    public $id;
    public $username;
    public $firstname;
    public $lastname;
    public $description;
    public $email;
    public $enabled;
    public $password;
    public $last_login;
    public $locked;
    public $expired;
    public $expires_at;
    public $confirmation_token;
    public $password_requested_at;
    public $credentials_expired;
    public $credentials_expire_at;
    public $ip;
    public $subscription_date;
    public $facebook_id;
    public $linkedin_id;
    public $avatar;
    
    private $inputFilter;
    

    public function exchangeArray($data)
    {
        $this->id 					    = (isset($data['id'])) ? $data['id'] : null;
        $this->username 				= (isset($data['username'])) ? $data['username'] : null;
        $this->lastname 				= (isset($data['lastname'])) ? $data['lastname'] : null;
        $this->firstname 				= (isset($data['firstname'])) ? $data['firstname'] : null;
        $this->description 				= (isset($data['description'])) ? $data['description'] : null;
        $this->email 					= (isset($data['email'])) ? $data['email'] : null;
        $this->password 				= (isset($data['password'])) ? $data['password'] : null;
        $this->enabled 					= (isset($data['enabled'])) ? $data['enabled'] : null;      
        $this->last_login 				= (isset($data['last_login'])) ? $data['last_login'] : null;      
        $this->locked 					= (isset($data['locked'])) ? $data['locked'] : null;      
        $this->expired 					= (isset($data['expired'])) ? $data['expired'] : null;      
        $this->expires_at 				= (isset($data['expires_at'])) ? $data['expires_at'] : null;      
        $this->confirmation_token       = (isset($data['confirmation_token'])) ? $data['confirmation_token'] : null;
        $this->password_requested_at    = (isset($data['password_requested_at'])) ? $data['password_requested_at'] : null;
        $this->role                     = (isset($data['role'])) ? $data['role'] : null;
        $this->role_name                = (isset($data['role_name'])) ? $data['role_name'] : null;
        $this->credentials_expired      = (isset($data['credentials_expired'])) ? $data['credentials_expired'] : null; 
        $this->credentials_expire_at    = (isset($data['credentials_expire_at'])) ? $data['credentials_expire_at'] : null; 
        $this->ip 					    = (isset($data['ip'])) ? $data['ip'] : null; 
        $this->subscription_date        = (isset($data['subscription_date'])) ? $data['subscription_date'] : null; 
        $this->facebook_id				= (isset($data['facebook_id'])) ? $data['facebook_id'] : null;
        $this->linkedin_id				= (isset($data['linkedin_id'])) ? $data['linkedin_id'] : null;
        $this->avatar				    = (isset($data['avatar'])) ? is_array($data['avatar']) && !empty($data['avatar']) ? $data['avatar']['name'] : $data['avatar']  : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'                    =>$this->id,
            'username'              =>$this->username,
            'lastname'              =>$this->lastname,
            'firstname'             =>$this->firstname,
            'email'                 =>$this->email,
            'password'              =>$this->password, 
            'enabled'               =>$this->enabled,
            'last_login'            =>$this->last_login,
            'locked'                =>$this->locked,  
            'expired'               =>$this->expired,    
            'expires_at'            =>$this->expires_at,    
            'password_requested_at' =>$this->password_requested_at,
            'confirmation_token'    =>$this->confirmation_token,
            'role'                  =>$this->role,
            'role_name'             =>$this->role_name,
            'credentials_expired'   =>$this->credentials_expired,
            'credentials_expire_at' =>$this->credentials_expire_at,
            'ip'                    =>$this->ip,
            'subscription_date'     =>$this->subscription_date,
            'facebook_id'           =>$this->facebook_id,
            'linkedin_id'           =>$this->linkedin_id,
            'avatar'                =>$this->avatar,
            'description'           =>$this->description,
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
            'name' => 'username',
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
                        'max' => 100,
                    ],
                ],
            ],
        ]);
        $inputFilter->add([
            'name' => 'firstname',
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
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'lastname',
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
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        // Add validation rules for the "file" field.	 
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'avatar',
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
                        'minWidth'  => 64,
                        'minHeight' => 64,
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