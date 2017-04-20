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
use Zend\Validator\AbstractValidator;

class Module
{
    const VERSION = '3.0.3-dev';
    
    public function onBootstrap(MvcEvent $e)
    {
        $translator=$e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories'=>array(
                'Application\Storage\AuthStorage' => function($sm) {
                    return new \login\Storage\AuthStorage('Session');
                },
                'AuthService' => function($sm) {
                            //My assumption, you've alredy set dbAdapter
                            //and has users table with columns : user_name and pass_word
                            //that password hashed with md5
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,
                        #'users','username','password', 'MD5(?)');
                        'user','username','password');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('Application\Storage\AuthStorage'));
                    return $authService;
                },
            ),
        );
    }
}
