<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Feed;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\Adapter\Adapter;

class Module implements ConfigProviderInterface
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\PostTable::class => function($container) {
                    $tableGateway = $container->get(Model\PostTableGateway::class);
                    return new Model\PostTable($tableGateway);
                },
                Model\PostTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Post());
                    return new TableGateway('post', $dbAdapter, null, $resultSetPrototype);
                },
                Model\CommentTable::class => function($container) {
                    $tableGateway = $container->get(Model\CommentTableGateway::class);
                    return new Model\CommentTable($tableGateway);
                },
                Model\CommentTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Comment());
                    return new TableGateway('comment', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

	public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\FeedController::class => function($container) {
                    return new Controller\FeedController(
                        $container->get(Model\PostTable::class)
                    );
                },
                Controller\CommentController::class => function($container) {
                    return new Controller\CommentController(
                        $container->get(Model\CommentTable::class)
                    );
                },
            ],
        ];
    }

}
