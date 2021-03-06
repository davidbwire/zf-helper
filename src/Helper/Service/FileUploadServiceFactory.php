<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace Helper\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Helper\Service\FileUploadService;
use Interop\Container\ContainerInterface;

/**
 * Description of UploadServiceFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class FileUploadServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $uploadService = new FileUploadService($serviceLocator->get('LoggerService'));
        return $uploadService;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        $uploadService = new FileUploadService($container->get('LoggerService'));
        return $uploadService;
    }

}
