<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 00:01
 */

namespace Nybbl\AccessAcl\Provider\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Nybbl\AccessAcl\Provider\DoctrineRoleProvider;

/**
 * Class DoctrineRoleProviderFactory
 * @package Nybbl\AccessAcl\Provider\Factory
 */
class DoctrineRoleProviderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return DoctrineRoleProvider|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new DoctrineRoleProvider($container->get('doctrine.entitymanager.orm_default'));
    }
}