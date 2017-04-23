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
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $serviceManager = $e->getApplication()->getServiceManager();
        $moduleRouteListener->attach($eventManager);
        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one.
        $sessionManager = $serviceManager->get(SessionManager::class);
    }
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [];
    }
}
