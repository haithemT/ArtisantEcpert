<?php
namespace Poll\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
class PollTable
{
    private $tableGateway;

    public $id;
    public $question;
    public $status;
    public $last_updated;
    public $created;
    public $created_by;
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
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
        return $poll;
    }

    public function savePoll(Poll $poll)
    {
        $data = [
            'id'                    => $poll->id,
            'question'              => $poll->question,
            'status'                => $poll->status,
            'event_id'              => $poll->event_id,
            'created'               => new Expression('NOW()'),
            'created_by'            => $poll->created_by,
        ];
        try {
            $connection = $this->dbAdapter->getDriver()->getConnection();
            $connection->beginTransaction();
            $insert = new Insert();
            $insert->into('poll')->values($data);
            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insert);
            $insertedID = $statement->execute()->getGeneratedValue();
            // iinsert into response table
            
            
            $connection->commit();
        } catch (\Exception $e) {
            if ($connection instanceof \Zend\Db\Adapter\Driver\ConnectionInterface) {
                $connection->rollback();
            }
            
            /* Other error handling */
        }
        $id = (int) $poll->id;

        if ($id === 0) {

            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getPoll($id)) {
            throw new RuntimeException(sprintf('Cannot update post with identifier %d; does not exist',$id));
        }
        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deletePoll($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}