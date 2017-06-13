<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use User\Model\User;
use Application\Form\LoginForm;
use User\Model\UserTable;
use Zend\Session\SessionManager;


/**
 * Login controller
 */
class LoginController extends AbstractActionController
{
    private  $authService;
    private  $userTable;
    private  $sessionManager;
    
    public function __construct(AuthenticationService $authService,UserTable $userTable, SessionManager $sessionManager)
    {
        $this->authService = $authService;
        $this->userTable = $userTable;
        $this->sessionManager = $sessionManager;
    }
    public function loginAction()
    {
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }
        if ($this->authService->getIdentity()!=null) {
            throw new \Exception('Already logged in');
        }
        
        $user = new User;
        $form = new LoginForm($user,'login');
        $messages = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
            //$form->setValidationGroup('usernameOrEmail', 'password', 'rememberme', 'csrf', 'captcha');
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $adapter = $this->authService->getAdapter();
                $usernameOrEmail = $this->params()->fromPost('usernameOrEmail');
                $adapter->setIdentity($usernameOrEmail);
                $adapter->setCredential($request->getPost('password')); 
                //$select = $adapter->getDbSelect();
                //check email also
                //$select->where->OR->equalTo( 'email', $usernameOrEmail );

                $result = $this->authService->authenticate();
                if ($result->isValid()) {
                   $identityRow = (array) $adapter->getResultRowObject();
                    if($identityRow['enabled'] == 0) {
                        $messages = $this->translator()->translate('Your username is disabled. Please contact an administrator.');
                        return new ViewModel([
                            'error' => $this->translator()->translate('Account disabled'),
                            'form'	=> $form,
                            'messages' => $messages
                        ]);
                    }elseif($identityRow['locked'] == 1){
                        $messages = $this->translator()->translate('Your username is locked. Please contact an administrator.');
                        return new ViewModel([
                            'error' => $this->translator()->translate('Account locked'),
                            'form'	=> $form,
                            'messages' => $messages
                        ]);
                    }
                    $this->authService->getStorage()->write(
                        [
                            'id'          => $identityRow['id'],
                            'username'    => $identityRow['username'],
                            'ip_address'  => $request->getServer('REMOTE_ADDR'),
                            'user_agent'  => $request->getServer('HTTP_USER_AGENT')
                        ]
                    );
                    // set remember me
                    if ($result->getCode()==Result::SUCCESS ) {
                        $this->sessionManager->rememberMe();
                    }elseif ($result->getCode() == Result::SUCCESS) {
                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    
                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack 
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if(empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                }
       
                }else{
                    $message = 'The username or email is not valid!';
                    return new ViewModel([
                        'error' => $this->translator()->translate('Your authentication credentials are not valid'),
                        'form'	=> $form,
                        'messages' => $message
                    ]);
                }
            }
        }
        
        return new ViewModel([
            'error' => $this->translator()->translate('Your authentication credentials are not valid'),
            'form'	=> $form,
            'messages' => $messages
        ]);
    }
    
    public function logoutAction()
    {
        if ($this->authService->hasIdentity()) {
            $this->authService->clearIdentity();
        }
        return $this->redirect()->toRoute('login');
    }
    
    public function registerAction()
    {
        if ($this->authService->getIdentity()!=null) {
            throw new \Exception('Already logged in');
        }
        $user = new User;
        $form = new LoginForm($user,'signup');
        $messages = null;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $currentDate = date('Y-m-d H:i:s');
            //$form->setValidationGroup('username','email','firstname','lastname', 'password','passwordConfirm', 'registration_type', 'csrf', 'captcha');
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                //CHECK EMAIL
                if($this->userTable->checkUserExists($data['email'])) {
                    return new ViewModel([
                        'error' => $this->translator()->translate('The email is already used'),
                        'form'	=> $form,
                        'messages' => $messages
                    ]);
                }
                $user->exchangeArray($form->getData());
                $user->password= $this->userTable->encryptPassword($data['password']);
                $user->last_login=$currentDate;
                $user->subscription_date=$currentDate;
                $user->ip=$request->getServer('REMOTE_ADDR');
                if($this->userTable->saveUser($user)){
                    $this->redirect()->toRoute('confirmSignUp');
                }  else {
                    return new ViewModel([
                        'error' => $this->translator()->translate('Cannot save user!'),
                        'form'	=> $form,
                        'messages' => $messages
                    ]);
                }
            }else{
                return new ViewModel([
                    'error' => $this->translator()->translate('Your authentication credentials are not valid'),
                    'form'	=> $form,
                    'messages' => $messages
                ]);
            }
        }
        return new ViewModel([
            'error' => '',
            'form'	=> $form,
            'messages' => $messages
        ]);
        
    }
    
    public function confirmSignUpAction()
    {
            
    }
}