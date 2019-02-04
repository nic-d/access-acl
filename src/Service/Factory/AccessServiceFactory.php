<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:04
 */

namespace Nybbl\AccessAcl\Service\Factory;

use Nybbl\AccessAcl\Service\AclService;
use Nybbl\AccessAcl\Service\AccessService;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class AccessServiceFactory
 * @package Nybbl\AccessAcl\Service\Factory
 */
class AccessServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AccessService|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $eventManager = $container->get(EventManagerInterface::class);
        $authenticationService = $container->get(AuthenticationService::class);
        $aclService = $container->get(AclService::class);
        $config = $container->get('config')['access_manager'];

        return new AccessService($eventManager, $authenticationService, $aclService, $config);
    }
}