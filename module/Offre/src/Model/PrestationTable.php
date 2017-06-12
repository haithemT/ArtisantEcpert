<?php
namespace Blog\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
class PostTable
{
    private $tableGateway;

    public $id;
    public $intitule;
    public $intitule_devis;
    public $description;
    
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rows=[];
        $select = new Select();
        $select->from('prestation');
        //$select->join('user', 'post.author_id = user.id', array('username', 'lastname', 'firstname'), 'left');
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getPrestation($id)
    {
        $post =new Post();
        $select = new Select();
        $select->from('prestation');
        //$select->join('user', 'post.author_id = user.id', array('username', 'lastname', 'firstname'), 'left');
        $select->where->equalTo('prestation.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $post->exchangeArray($resultSet->current());
        return $post;
    }
    public function savePrestation(Prestation $prestation)
    {
        $data = [
            'id'                        =>$prestation->id,
            'intitule'                  =>$prestation->intitule,
            'intitule_devis'            =>$prestation->intitule_devis,
            'description'               =>$prestation->description,
        ];
        $id = (int) $prestation->id;

        if ($id === 0) {
            $data['date'] = new Expression('NOW()');
            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getPost($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update prestation with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deletePrestation($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}