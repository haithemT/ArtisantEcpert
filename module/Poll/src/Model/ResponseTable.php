<?php
namespace Poll\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Insert;
class ResponseTable
{
    private $tableGateway;

    public $id;
    public $text;
    public $poll_id;
    public $count;
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getResponse($id)
    {
        $response =new Response();
        $select = new Select();
        $select->from('response');
        $select->where->equalTo('id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $response->exchangeArray($resultSet->current());
        return $response;
    }

    public function saveResponse(Response $response)
    {
        $data = [
            'id'         => $response->id,
            'poll_id'    => $response->poll_id,
            'text'       => $response->text,
            'count'      => $response->count,
        ];
        $id = (int) $response->id;

        if ($id === 0) {
            return $this->tableGateway->insert($data);
        }

        if (! $this->getResponse($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update post with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function fetchAll($id=null)
    {
        $rows=[];
        $select = new Select();
        $select->from('response');
        $select->where('poll_id',$id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function deleteResponse($id)
    {
        return $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function deletePollResponses($id)
    {
        return $this->tableGateway->delete(['poll_id' => (int) $id]);
    }

}