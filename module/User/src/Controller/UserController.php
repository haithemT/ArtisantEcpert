<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;

use User\Model\UserTable;
use User\Form\UserForm;
use User\Model\User;
use User\Service\UserService as UserCredentialsService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    private $table;

    public function __construct(UserTable $table)
    {
        $this->table = $table;
    }

 	public function indexAction()
    {
        return new ViewModel([
            'users' => $this->table->fetchAll(),
        ]);

    }

    public function addAction()
    {
        $form = new UserForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
        } else {
            return ['form' => $form];
        }
        $user = new User();

        $form->setInputFilter($user->getInputFilter());
        $form->setData($data);
        if (! $form->isValid()) {
            return ['form' => $form];
        }   
        $user->exchangeArray($form->getData());
           /**
         * 
         * SET CONNECTED USER IP
         * @todo get the real connected user ip not the proxied ip
         */
        $user->ip=$request->getServer('REMOTE_ADDR');
        $user->confirmation_token=md5(uniqid(mt_rand(), true));
        $user->password=UserCredentialsService::encryptPassword($user->password);

        $user->created_by=$this->identity()['id'];     
        $this->table->saveUser($user);
        return $this->redirect()->toRoute('user');
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
        $imageFilter = $form->getInputFilter()->get('avatar');
        $imageFilter->setRequired(false);

        $request = $this->getRequest();
        $viewData = ['user' => $user, 'form' => $form];

        if ($request->isPost()) {
            if($request->getFiles()){
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
            } else {
                $data = $request->getPost();
            }
        } else {
            return $viewData;
        }


        $form->setInputFilter($user->getInputFilter());
        $form->setData($data);

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
