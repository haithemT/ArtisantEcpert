<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;
use Application\Service\Factory\AppControllerFactory;
use Zend\Session\SessionManager;

class Module
{
    const VERSION = '3.0.3-dev';
    
    protected $whitelist = [
        'Application\Controller\LoginController'
    ];
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $serviceManager = $e->getApplication()->getServiceManager();
        $moduleRouteListener->attach($eventManager);
        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
        $sessionManager = $serviceManager->get(SessionManager::class);
        $eventManager->attach('dispatch', [$this, 'setLayout']);
        $eventManager->attach('dispatch', [$this, 'checkLogin']);
    }
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [];
    }
    
    public function setLayout($e) {
        $matches = $e->getRouteMatch();

        $controller = $matches->getParam('controller');

        $module_array = explode('\\', $controller);
        $module = $module_array[0];

        // Set the layout template
       /* $viewModel = $e->getViewModel();
        // Top menu
        $topMenuView = new ViewModel();
        $topMenuView->setTemplate('content/top_menu');
        // Header
        $headerView = new ViewModel();
        $headerView->setTemplate('content/header');
        // Primary sidebar
        $sidebarPrimaryView = new ViewModel();
        $sidebarPrimaryView->setTemplate('content/sidebar_primary');

        $viewModel->addChild($topMenuView, 'top_menu');
        $viewModel->addChild($headerView, 'header');
        $viewModel->addChild($sidebarPrimaryView, 'sidebar_primary');*/

        /*if ('Login\Controller\Auth' === $controller) {
            $viewModel->setTemplate('login/layout');
            return;
        }*/

       // $viewModel->setTemplate('content/layout');
        $this->loadScripts($e, $module);
        $this->loadStyles($e, $module);
    }
    public function checkLogin($e){
        $match = $e->getRouteMatch();
        $sm = $e->getApplication()->getServiceManager();
        $auth = $sm->get(AuthenticationService::class);
        $target = $e->getTarget();
        $controller = $match->getParam('controller');
        // Route is whitelisted
        if( !in_array($controller, $this->whitelist)){
            if( !$auth->hasIdentity() ){
                return $target->redirect()->toRoute('login');
            }
        }
    }
    
        public function loadScripts($e, $module) {
        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('Config');
        //Check filesLoading array in configuration for the current module
        $filesLoadingConfig = isset($config['filesLoading']) ? $config['filesLoading'] : array();
        if ($filesLoadingConfig && isset($filesLoadingConfig[$module])) {
            $scriptsList = isset($filesLoadingConfig[$module]['scripts']) ? $filesLoadingConfig[$module]['scripts'] : array();
            //load view helper manager
            $viewHelperManager = $sm->get('viewhelpermanager');
            $inlineScript = $viewHelperManager->get('inlineScript');
            $basePathPlugin = $viewHelperManager->get('basePath');
            $basePath = $basePathPlugin();
            //add scripts inline in the bottom of the body
            foreach ($scriptsList as $value) {
                $inlineScript->appendFile($basePath . $value);
            }
        }
    }

    public function loadStyles($e, $module) {
        $sm = $e->getApplication()->getServiceManager();
        $config = $sm->get('Config');
        //Check filesLoading array in configuration for the current module
        $filesLoadingConfig = isset($config['filesLoading']) ? $config['filesLoading'] : array();
        if ($filesLoadingConfig && isset($filesLoadingConfig[$module])) {
            $stylesList = isset($filesLoadingConfig[$module]['styles']) ? $filesLoadingConfig[$module]['styles'] : array();
            //load view helper manager
            $viewHelperManager = $sm->get('viewhelpermanager');
            $headLink = $viewHelperManager->get('headLink');
            $basePathPlugin = $viewHelperManager->get('basePath');
            $basePath = $basePathPlugin();
            //add styles in header
            foreach ($stylesList as $value) {
                $headLink->prependStylesheet(array('href' => $basePath . $value, 'rel' => 'stylesheet', 'media' => 'all'));
            }
        }
    }
}
