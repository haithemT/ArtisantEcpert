<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License 
 */

namespace Feed\Controller;

use Feed\Model\CommentTable;
use Feed\Form\CommentForm;
use Feed\Model\Comment;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CommentController extends AbstractActionController
{
    private $table;

    public function __construct(CommentTable $table)
    {
        $this->table = $table;
    }

 	public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (0 === $id) {
            $comments = $this->table->fetchAll();
        }else {
            $comments = $this->table->fetchAllByPost($id);
        }
        return new ViewModel([
            'comments' => $comments,
        ]);

    }

    public function addAction()
    {
        $form = new CommentForm();
        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $comment = new Comment();

        $form->setInputFilter($comment->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $comment->exchangeArray($form->getData());
        /**
         * 
         * SET CONNECTED USER IP
         * @todo get the real connected user ip not the proxied ip
         */
        $comment->author_id=$this->identity()['id'];        
        $this->table->saveComment($comment);
        return $this->redirect()->toRoute('comment');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('comment', ['action' => 'add']);
        }

        // Retrieve the user with the specified id. Doing so raises
        // an exception if the user is not found, which should result
        // in redirecting to the landing page.
        try {
            $comment = $this->table->getComment($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('comment', ['action' => 'index']);
        }

        $form = new CommentForm();
        $form->bind($comment);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form, 'post_id' => $comment->post_id];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($comment->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        $this->table->saveComment($comment);

        // Redirect to users list
        return $this->redirect()->toRoute('comment', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('comment');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteComment($id);
            }

            // Redirect to list of posts
            return $this->redirect()->toRoute('comment');
        }

        return [
            'id'    => $id,
            'comment' => $this->table->getComment($id),
        ];
    }
}
