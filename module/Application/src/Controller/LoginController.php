<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Storage\AuthStorage;
use User\Model\User;
use Application\Form\LoginForm;

/**
 * Login controller
 */
class LoginController extends AbstractActionController
{
    protected $storage;
    protected $authservice; 
    protected $userTable; 
    
    public function loginAction()
    {
        if ($user = $this->identity()) {
            return $this->redirect()->toRoute('/');
        }
        
        $user = new User;
        $form = new LoginForm($user);
        $messages = null;
        if ($this->getRequest()->isPost()) {
            $form->setValidationGroup('usernameOrEmail', 'password', 'rememberme', 'csrf', 'captcha');
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $authService = $this->getAuthService();
                $adapter = $authService->getAdapter();
                $usernameOrEmail = $this->params()->fromPost('usernameOrEmail');
                $adapter->setIdentity($request->getPost('username'));
                $adapter->setCredential($request->getPost('password')); 
                $select = $adapter->getDbSelect();
                $select->where->OR->equalTo( 'email', $usernameOrEmail );
             
                $result = $this->getAuthService()->authenticate();
                if ($result->isValid()) {
                   $identityRow = (array) $this->getAuthService()->getAdapter()->getResultRowObject();
                    if($identityRow['enabled'] == 0) {
                        $messages = $this->translate('Your username is disabled. Please contact an administrator.');
                        return new ViewModel([
                            'error' => $this->translate('Account disabled'),
                            'form'	=> $form,
                            'messages' => $messages
                        ]);
                    }elseif($identityRow['locked'] == 1){
                        $messages = $this->translate('Your username is locked. Please contact an administrator.');
                        return new ViewModel([
                            'error' => $this->translate('Account locked'),
                            'form'	=> $form,
                            'messages' => $messages
                        ]);
                    }
                    $this->getAuthService()->getStorage()->write(
                        [
                            'id'          => $identityRow['id'],
                            'username'    => $dataform['username'],
                            'ip_address'  => $this->getRequest()->getServer('REMOTE_ADDR'),
                            'user_agent'  => $request->getServer('HTTP_USER_AGENT')
                        ]
                    );
                    if ($this->params()->fromPost('rememberme')) {
                        $time = 1209600; // 14 days (1209600/3600 = 336 hours => 336/24 = 14 days)
                        $AuthStorage = new AuthStorage();
                        $AuthStorage->setRememberMe($time);
                    }        
                }else{
                    $message = 'The username or email is not valid!';
                    return new ViewModel(array(
                        'error' => $this->translate('Your authentication credentials are not valid'),
                        'form'	=> $form,
                        'messages' => $messages
                    ));
                }
            }
        }
        
        return new ViewModel(array(
            'error' => $this->translate('Your authentication credentials are not valid'),
            'form'	=> $form,
            'messages' => $messages
        ));
    }
    
    public function logoutAction()
    {
        $authService = $this->getAuthService();
        if ($authService->hasIdentity()) {
            $authService->clearIdentity();
            $AuthStorage = new AuthStorage();
            $AuthStorage->forgetMe();
        }
        return $this->redirect()->toRoute('/login');
    }
    
    public function registerAction()
    {
        return new ViewModel();
    }
    
    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()
                                      ->get('AuthService');
        }         
        return $this->authservice;
    }
     
    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()->get('Application\Storage\AuthStorage');
        }         
        return $this->storage;
    }    
    
   public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
}