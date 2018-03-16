<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Authentication\AuthenticationService;
use Application\Service\Factory\AuthenticationServiceFactory;
use Application\Controller\Plugin\Factory\TranslatorPluginFactory;


return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'register' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/register',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'register',
                    ],
                ],
            ],
            'confirmSignUp' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/confirm',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'confirmSignUp',
                    ],
                ],
            ],
            'images' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/images',
                    'defaults' => [
                        'controller' => Controller\ImageManagerController::class,
                        'action'     => 'file',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\LoginController::class => Controller\Factory\LoginControllerFactory::class,
            Controller\IndexController::class => InvokableFactory::class,
            Controller\ImageManagerController::class => Controller\Factory\ImageManagerControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\MailSender::class => InvokableFactory::class,
            AuthenticationService::class => AuthenticationServiceFactory::class,
            User\Service\UserService::class => AuthenticationServiceFactory::class,
            Service\ImageService::class => InvokableFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'translator' => TranslatorPluginFactory::class,
        ],
    ],
    'session_containers' => [
        'ContainerNamespace'
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
