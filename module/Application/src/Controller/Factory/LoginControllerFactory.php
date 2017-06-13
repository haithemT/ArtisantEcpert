<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Application\Controller\LoginController;
use Zend\ServiceManager\Factory\FactoryInterface;
/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);
        $sessionManager = $container->get(Zend\Session\SessionManager::class);
        $userTable = $container->get(\User\Model\UserTable::class);
        return new LoginController($authService,$userTable,$sessionManager);
    }
}