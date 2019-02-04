<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:45
 */

namespace Nybbl\AccessAcl\Event\Factory;

use Nybbl\AccessAcl\Event\DispatchEvent;
use Interop\Container\ContainerInterface;
use Nybbl\AccessAcl\Service\AccessService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class DispatchEventFactory
 * @package Nybbl\AccessAcl\Event\Factory
 */
class DispatchEventFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DispatchEvent|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $accessService = $container->get(AccessService::class);
        $config = $container->get('config')['access_manager'];

        return new DispatchEvent($accessService, $config);
    }
}