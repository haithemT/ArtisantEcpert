<?php
namespace Blog\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Expression;

class PostTable
{
    private $tableGateway;

    public $id;
    public $author_id;
    public $status;
    public $title;
    public $content;
    public $excerpt;
    public $post_date;
    public $updated;
    
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

    public function getArticle($id)
    {
        $select = new Select();
        $select->from('post');
        $select->join('user', 'post.author_id = user.id', array('username', 'lastname', 'firstname'), 'left');
        $select->where->equalTo('post.id', $id);
        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();
        return $resultSet->current();
    }
    public function saveArticle(Article $article)
    {
        $data = [
            'id'                    =>$article->id,
            'author_id'             =>$article->author_id,
            'status'                =>$article->status,
            'title'                 =>$article->title,
            'content'               =>$article->content,
            'excerpt'               =>$article->excerpt, 
            'post_date'             =>$article->post_date,
            'updated'               =>$article->updated,
        ];
        $id = (int) $article->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return 1;
        }

        if (! $this->getArticle($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update article with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteArticle($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

}