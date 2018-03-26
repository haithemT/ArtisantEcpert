<?php
namespace Poll\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Poll\Controller\PollController;
use Poll\Model\PollTable;
use Event\Model\EventTable;
/**
 * This is the factory for PollController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class PollControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $pollTable  = $container->get(PollTable::class);
        $eventTable = $container->get(EventTable::class);
        // Instantiate the controller and inject dependencies
        return new PollController($pollTable,$eventTable);
    }
}