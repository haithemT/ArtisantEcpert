<?php
namespace Event\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Event\Controller\EventController;
use Event\Model\EventTable;
/**
 * This is the factory for EventController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class EventControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $eventTable = $container->get(EventTable::class);
        // Instantiate the controller and inject dependencies
        return new EventController($eventTable);
    }
}