<?php
namespace Feed\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
class PostTable
{
    private $tableGateway;

    public $id;
    public $author_id;
    public $status;
    public $title;
    public $content;
    public $excerpt;
    public $comments;
    public $karma;
    public $post_date;
    public $updated;
    public $picture;
    
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rows=[];
        $post =new Post();
        $select = new Select();
        $select->from('post');
        $select->join('comments', 'comments.post_id = post.id', array('commentsCount' => new \Zend\Db\Sql\Expression('COUNT(comments.post_id)')), 'left');
        $select->join('karma', 'karma.post_id = post.id', array('karmaCount' => new \Zend\Db\Sql\Expression('COUNT(karma.post_id)')), 'left');
        $select->join('user', 'user.id = post.author_id', array('username', 'lastname', 'firstname'), 'left');
       // echo $this->tableGateway->getSql()->getSqlstringForSqlObject($select); die ;
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $post->exchangeArray($row);
            $rows[] = $post;
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
            'picture'               =>$post->picture,
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

    public function getPostKarmadetails($postid)
    {
        $rows=[];
        $post =new Post();
        $select = new Select();
        $select->from('karma');
        $select->join('user', 'karma.author_id = user.id', array('author' => new \Zend\Db\Sql\Expression('CONCAT(lastname," ",firstname)')), 'left');
        $select->where->equalTo('post_id', $postid);
       // echo $this->tableGateway->getSql()->getSqlstringForSqlObject($select); die ;
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

}