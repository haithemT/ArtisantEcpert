<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Blog\Controller;

use Blog\Model\PostTable;
use Blog\Form\PostForm;
use Blog\Model\Post;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BlogController extends AbstractActionController
{
    private $table;

    public function __construct(PostTable $table)
    {
        $this->table = $table;
    }

 	public function indexAction()
    {
             print_r($this->identity());die;
        return new ViewModel([
            'posts' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new PostForm();
        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $post = new Post();
        $postStatus = $form->get('status');
        $postStatusList = [
            [
                'value' => 'publish',
                'label' => $this->translate('Publish'),               
            ],
            [
                'value' => 'draft',
                'label' => $this->translate('Draft'),               
            ],
            [
                'value' => 'pending',
                'label' => $this->translate('Pending'),               
            ],
        ];
        $postStatus->setValueOptions($postStatusList);
        
        $form->setInputFilter($post->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $post->exchangeArray($form->getData());
        /**
         * 
         * SET CONNECTED USER IP
         * @todo get the real connected user ip not the proxied ip
         */
        $post->author_id=$this->identity()['id'];
        $user->confirmation_token=md5(uniqid(mt_rand(), true));
        $user->password=UserCredentialsService::encryptPassword($user->password);
        
        $this->table->savePost($post);
        return $this->redirect()->toRoute('post');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('user', ['action' => 'add']);
        }

        // Retrieve the user with the specified id. Doing so raises
        // an exception if the user is not found, which should result
        // in redirecting to the landing page.
        try {
            $user = $this->table->getUser($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('user', ['action' => 'index']);
        }

        $form = new UserForm();
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($user->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        /**
         * 
         * SET CONNECTED USER IP
         * @todo get the real connected user ip not the proxied ip
         */
        $user->ip=$request->getServer('REMOTE_ADDR');
        $this->table->saveUser($user);

        // Redirect to users list
        return $this->redirect()->toRoute('user', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('user');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteUser($id);
            }

            // Redirect to list of users
            return $this->redirect()->toRoute('user');
        }

        return [
            'id'    => $id,
            'user' => $this->table->getUser($id),
        ];
    }
}
