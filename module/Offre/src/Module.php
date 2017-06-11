<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Offre;

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
                Model\PrestationTable::class => function($container) {
                    $tableGateway = $container->get(Model\PrestationTableGateway::class);
                    return new Model\PrestationTable($tableGateway);
                },
                Model\PrestationTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Prestation());
                    return new TableGateway('prestation', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

	public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\PrestationController::class => function($container) {
                    return new Controller\PrestationController(
                        $container->get(Model\PrestationTable::class)
                    );
                },
            ],
        ];
    }

}
