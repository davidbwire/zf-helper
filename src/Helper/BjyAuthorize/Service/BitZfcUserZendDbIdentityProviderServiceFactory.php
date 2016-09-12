<?php

namespace Helper\BjyAuthorize\Service;

use Helper\BjyAuthorize\Provider\Identity\BitZfcUserZendDb;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Description of BitZfcUserZendDbIdentityProviderServiceFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class BitZfcUserZendDbIdentityProviderServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter = $serviceLocator->get('zfcuser_zend_db_adapter');
        /* @var $userService \ZfcUser\Service\User */
        $userService = $serviceLocator->get('zfcuser_user_service');
        $config = $serviceLocator->get('BjyAuthorize\Config');

        $provider = new BitZfcUserZendDb($adapter, $userService);

        $provider->setDefaultRole($config['default_role']);

        return $provider;
    }

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null)
    {
        /* @var $adapter \Zend\Db\Adapter\Adapter */
        $adapter = $container->get('zfcuser_zend_db_adapter');
        /* @var $userService \ZfcUser\Service\User */
        $userService = $container->get('zfcuser_user_service');
        $config = $container->get('BjyAuthorize\Config');

        $provider = new BitZfcUserZendDb($adapter, $userService);

        $provider->setDefaultRole($config['default_role']);

        return $provider;
    }

}
