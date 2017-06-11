<?php
namespace User\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class FeedbackTable
{
    private $tableGateway;

    public $id;
    public $user_id;
    public $text;
    public $rate;
    public $date;
    public $highlight;
    public $user_full_name;
    
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rows=[];
        $select = new Select();
        $select->from('feedback');
        $select->join('user', 'feedback.user_id = user.id', array('username', 'lastname', 'firstname'), 'left');
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getFeedback($id)
    {
        $feedback =new Feedback();
        $select = new Select();
        $select->from('feedback');
        $select->join('user', 'feedback.user_id = user.id', array('username', 'lastname', 'firstname'), 'left');
        $select->where->equalTo('feedback.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $feedback->exchangeArray($resultSet->current());
        return $feedback;
    }
    public function saveFeedback(Feedback $feedback)
    {
        $data = [
            'id'                =>$feedback->id,
            'text'              =>$feedback->text,
            'rate'              =>$feedback->rate,
            'highlight'         =>$feedback->highlight,
        ];
        $id = (int) $feedback->id;

        if ($id === 0) {
            $data['date'] = new Expression('NOW()');
            $data['user_id'] = $feedback->user_id;
            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getFeedback($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update feedback with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteFeedback($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}