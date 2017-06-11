<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Offre\Controller;

use Offre\Model\PrestationTable;
use Offre\Form\PrestationForm;
use Offre\Model\Prestation;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PrestationController extends AbstractActionController
{
    private $table;

    public function __construct(PostTable $table)
    {
        $this->table = $table;
    }

 	public function indexAction()
    {
        return new ViewModel([
            'posts' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new PostForm();
        $request = $this->getRequest();
        $postStatusList = [
            'publish'   => $this->translator()->translate('Publish'),
            'draft'     => $this->translator()->translate('Draft'),
            'pending'   => $this->translator()->translate('Pending')
        ];
        $form->get('status')->setAttribute('options',$postStatusList);
        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $post = new Post();

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
        $this->table->savePost($post);
        return $this->redirect()->toRoute('blog');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('blog', ['action' => 'add']);
        }

        // Retrieve the user with the specified id. Doing so raises
        // an exception if the user is not found, which should result
        // in redirecting to the landing page.
        try {
            $post = $this->table->getPost($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('blog', ['action' => 'index']);
        }

        $form = new PostForm();
        $form->bind($post);
        $form->get('submit')->setAttribute('value', 'Edit');
        $form->get('status')->setValue($post->status);
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($post->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }
        $this->table->savePost($post);

        // Redirect to users list
        return $this->redirect()->toRoute('blog', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('post');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deletePost($id);
            }

            // Redirect to list of posts
            return $this->redirect()->toRoute('blog');
        }

        return [
            'id'    => $id,
            'post' => $this->table->getPost($id),
        ];
    }
}
