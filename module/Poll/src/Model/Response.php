<?php
namespace Poll\Model;

use DomainException;

class Response
{
    public $id;
    public $text;
    public $poll_id;
    public $count;
    

    public function exchangeArray($data)
    {
        $this->id 		            = (isset($data['id'])) ? $data['id'] : null;
        $this->text                 = (isset($data['text'])) ? $data['text'] : null;
        $this->poll_id              = (isset($data['poll_id'])) ? $data['poll_id'] : null;
        $this->count                = (isset($data['count'])) ? $data['count'] : null;
    }   
    
    public function getArrayCopy()
    {
        return [
            'id'                    => $this->id,
            'text'                  => $this->text,
            'poll_id'               => $this->poll_id,
            'count'                 => $this->count,
        ];
    }
}