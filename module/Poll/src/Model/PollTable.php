<?php
namespace Poll\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;

class PollTable
{
    private $tableGateway;

    public $id;
    public $question;
    public $status;
    public $last_updated;
    public $created;
    public $created_by;
    
    public function __construct(TableGatewayInterface $tableGateway, ResponseTable $responseTable)
    {
        $this->tableGateway = $tableGateway;
        $this->responseTable = $responseTable;
    }

    public function fetchAll()
    {
        $rows=[];
        $select = new Select();
        $select->from('poll');
        $select->join('user', 'poll.created_by = user.id', array('username', 'lastname', 'firstname'), 'left');
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $row['response'] = $this->responseTable->fetchAll($row['id']);
            $rows[] = $row;
        }
        return $rows;
    }

    public function getPoll($id)
    {
        $poll =new Poll();
        $select = new Select();
        $select->from('poll');
        $select->join('user', 'poll.created_by = user.id', array('username', 'lastname', 'firstname'), 'left');
        $select->where->equalTo('poll.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $poll->exchangeArray($resultSet->current());
        $poll->response = $this->responseTable->fetchAll($id);
        return $poll;
    }

    public function addPoll(Poll $poll)
    {
        //echo '<pre>';print_r($poll);die;
        $connection = null;
        $data = [
            'id'                    => $poll->id,
            'question'              => $poll->question,
            'status'                => $poll->status,
            'event_id'              => $poll->event_id,
            'created'               => new Expression('NOW()'),
            'last_updated'          => new Expression('NOW()'),
            'created_by'            => $poll->created_by,
        ];
        try {
            $connection = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connection->beginTransaction();
            $insert = new Insert();
            $insert->into('poll')->values($data);
            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insert);
            $insertedID = $statement->execute()->getGeneratedValue();
            // insert into responses tables with new pollID
            if($insertedID) {
                foreach($poll->response as $rs) {
                    $response =new Response();
                    $d = [
                        'poll_id'   => $insertedID,
                        'text'      => $rs,
                        'count'     => 0,
                    ];
                    $response->exchangeArray($d);
                    $this->responseTable->saveResponse($response);
                }
                $connection->commit();
            }
            return 1; 
        } catch (\Exception $e) {
            if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                $connection->rollback();
            }
            
            /* Other error handling */
        }
    }

    public function updatePoll(Poll $poll)
    {
        $connection = null;
        $data = [
            'question'              => $poll->question,
            'status'                => $poll->status,
            'event_id'              => $poll->event_id,
            'last_updated'          => new Expression('NOW()'),
        ];
        try {
            $connection = $this->tableGateway->getAdapter()->getDriver()->getConnection();
            $connection->beginTransaction();
            $update = new Update();
            $update->table('poll');
            $update->set($data);
            $update->where->equalTo('poll.id', $poll->id); 
            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);
            if($poll->response) {
                $deleted = $this->responseTable->deletePollResponses($poll->id);
                $updated = $statement->execute()->getAffectedRows();
                // insert into responses tables with new pollID
                foreach($poll->response as $rs) {
                    $response = new Response();
                    $d = [
                        'poll_id'   => $poll->id,
                        'text'      => $rs,
                        'count'     => 0,
                    ];
                    $response->exchangeArray($d);
                    $this->responseTable->saveResponse($response);
                }
                $connection->commit();
                return 1;
            } else {
                throw new \Exception('you need to provide a response.');
            }

        } catch (\Exception $e) {
            if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                $connection->rollback();
            }
            
            /* Other error handling */
        }
    }


    public function deletePoll($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}