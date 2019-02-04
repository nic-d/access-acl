<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 14:03
 */

namespace Nybbl\AccessAcl\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Nybbl\AccessAcl\Service\AccessService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Nybbl\AccessAcl\Controller\Plugin\AssertionControllerPlugin;

/**
 * Class AssertionControllerPluginFactory
 * @package Nybbl\AccessAcl\Controller\Plugin\Factory
 */
class AssertionControllerPluginFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AssertionControllerPlugin|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $accessService = $container->get(AccessService::class);
        return new AssertionControllerPlugin($accessService);
    }
}