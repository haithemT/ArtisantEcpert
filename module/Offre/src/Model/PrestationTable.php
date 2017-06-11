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
        $select->from('post');
        $select->join('user', 'post.author_id = user.id', array('username', 'lastname', 'firstname'), 'left');
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getPost($id)
    {
        $post =new Post();
        $select = new Select();
        $select->from('post');
        $select->join('user', 'post.author_id = user.id', array('username', 'lastname', 'firstname'), 'left');
        $select->where->equalTo('post.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $post->exchangeArray($resultSet->current());
        return $post;
    }
    public function savePost(Post $post)
    {
        $data = [
            'id'                    =>$post->id,
            'status'                =>$post->status,
            'title'                 =>$post->title,
            'content'               =>$post->content,
            'excerpt'               =>$post->excerpt,
            'updated'               =>new Expression('NOW()'),
        ];
        $id = (int) $post->id;

        if ($id === 0) {
            $data['post_date'] = new Expression('NOW()');
            $data['author_id'] = $post->author_id;
            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getPost($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update post with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deletePost($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}