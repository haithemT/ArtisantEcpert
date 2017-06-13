<?php
namespace Offre\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class OffreTable
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
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getPrestation($id)
    {
        $prestation =new Prestation();
        $select = new Select();
        $select->from('prestation');
        $select->where->equalTo('prestation.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $prestation->exchangeArray($resultSet->current());
        return $prestation;
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
            //$data['date'] = new Expression('NOW()');
            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getPrestation($id)) {
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