<?php
namespace Event\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
class EventTable
{
    private $tableGateway;

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
    public $updated_by;
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rows=[];
        $select = new Select();
        $select->from('event');
        $select->join('user', 'event.organizer_contact = user.id', array('username', 'lastname', 'firstname'), 'left');
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getEvent($id)
    {
        $event =new Event();
        $select = new Select();
        $select->from('event');
        //$select->join('user', 'event.organizer_contact = user.id', array('username', 'lastname', 'firstname'), 'left');
        //$select->join('country', 'event.country = country.id', array('name' => 'countryName'), 'left');
        //$select->join('city', 'event.city = city.id', array('name' => 'cityName'), 'left');
        $select->where->equalTo('event.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $event->exchangeArray($resultSet->current());
        return $event;
    }

    public function saveEvent(Event $event)
    {
        $data = [
            'id'                    => $event->id,
            'organizer_contact'     => $event->organizer_contact,
            'organizer'             => $event->organizer,
            'status'                => $event->status,
            'eventName'             => $event->eventName,
            'description'           => $event->description,
            'startDate'             => $event->startDate, 
            'endDate'               => $event->endDate,
            'facebook'              => $event->facebook,
            'twitter'               => $event->twitter,
            'twitterHashTag'        => $event->twitterHashTag,
            'instagram'             => $event->instagram,
            'country'               => $event->country,
            'city'                  => $event->city,
            'address'               => $event->address,
            'last_updated'          => new Expression('NOW()'),
            'updated_by'            => $event->updated_by,
        ];
        $id = (int) $event->id;

        if ($id === 0) {
            $data['created'] = new Expression('NOW()');
            $data['created_by'] = $event->created_by;
            $this->tableGateway->insert($data);
            return 1;
        }
        if($event->picture){
            $data['picture'] = $event->picture;
        }

        if (! $this->getEvent($id)) {
            throw new RuntimeException(sprintf('Cannot update post with identifier %d; does not exist',$id));
        }
        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteEvent($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}