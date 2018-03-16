<?php
namespace Feed\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class CommentTable
{
    private $tableGateway;

    public $id;
    public $author_id;
    public $post_id;
    public $comment;
    public $ip;
    public $approved;
    public $created;
    public $updated;
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rows=[];
        $comment =new Comment();
        $select = new Select();
        $select->from('comments');
        $select->join('user', 'user.id = comments.author_id', array('username', 'lastname', 'firstname'), 'left');
       // echo $this->tableGateway->getSql()->getSqlstringForSqlObject($select); die ;
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $comment->exchangeArray($row);
            $rows[] = $comment;
        }
        return $rows;
    }

    public function fetchAllByPost($id)
    {
        $rows=[];
        $comment =new Comment();
        $select = new Select();
        $select->from('comments');
        $select->join('user', 'user.id = comments.author_id', array('username', 'lastname', 'firstname'), 'left');
       // echo $this->tableGateway->getSql()->getSqlstringForSqlObject($select); die ;
        $select->where->equalTo('comments.post_id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $comment->exchangeArray($row);
            $rows[] = $comment;
        }
        return $rows;
    }

    public function getComment($id)
    {
        $comment =new Comment();
        $select = new Select();
        $select->from('comments');
        $select->join('user', 'comments.author_id  = user.id', array('username', 'lastname', 'firstname'), 'left');
        $select->where->equalTo('comments.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        $comment->exchangeArray($resultSet->current());
        return $comment;
    }
    public function saveComment(Comment $comment)
    {
        $data = [
            'post_id'    => $comment->post_id,
            'comment'    => $comment->comment,
            'ip'         => $comment->ip,
            'approved'   => $comment->approved,
            'updated'    => new Expression('NOW()'),
        ];
        $id = (int) $comment->id;

        if ($id === 0) {
            $data['created'] = new Expression('NOW()');
            $data['author_id'] = $comment->author_id;
            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getComment($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update post with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteComment($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}