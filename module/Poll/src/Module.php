<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Poll;

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
                Model\PollTable::class => function($container) {
                    $tableGateway = $container->get(Model\PollTableGateway::class);
                    $responseTable = $container->get(Model\ResponseTable::class);
                    return new Model\PollTable($tableGateway,$responseTable);
                },
                Model\PollTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Poll());
                    return new TableGateway('poll', $dbAdapter, null, $resultSetPrototype);
                },
                Model\ResponseTable::class => function($container) {
                    $tableGateway = $container->get(Model\ResponseTableGateway::class);
                    return new Model\ResponseTable($tableGateway);
                },
                Model\ResponseTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Response());
                    return new TableGateway('response', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

	// public function getControllerConfig()
    // {
    //     return [
    //         'factories' => [
    //             Controller\EventController::class => function($container) {
    //                 return new Controller\EventController(
    //                     $container->get(Model\EventTable::class)
    //                 );
    //             },
    //         ],
    //     ];
    // }

}
