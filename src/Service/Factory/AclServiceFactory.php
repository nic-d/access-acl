<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:13
 */

namespace Nybbl\AccessAcl\Service\Factory;

use Nybbl\AccessAcl\Service\AclService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Nybbl\AccessAcl\Contract\RoleProviderInterface;

/**
 * Class AclServiceFactory
 * @package Nybbl\AccessAcl\Service\Factory
 */
class AclServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AclService|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $roleProvider = $container->get(RoleProviderInterface::class);
        $config = $container->get('config')['access_manager'];

        return new AclService($roleProvider, $config);
    }
}