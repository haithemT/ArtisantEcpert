<?php
namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
/**
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * This method creates the Zend\Authentication\AuthenticationService service 
     * and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //My assumption, you've alredy set dbAdapter
        //and has users table with columns : user_name and pass_word
        //that password hashed with md5
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage('Zend_Auth', 'session', $sessionManager);
        $dbAdapter = $container->get(\Zend\Db\Adapter\Adapter::class);
        $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,
            #'users','username','password', 'MD5(?)');
            'user','username','password');
        return new AuthenticationService($authStorage, $dbTableAuthAdapter);
    }
}