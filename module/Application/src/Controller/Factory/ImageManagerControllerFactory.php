<?php 

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Service\ImageService;
use Application\Controller\ImageManagerController;

class ImageManagerControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $imageService = $container->get(ImageService::class);
        return new ImageManagerController($imageService);
    }
}