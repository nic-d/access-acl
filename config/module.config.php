<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:02
 */

use Nybbl\AccessAcl\Event;
use Nybbl\AccessAcl\Service;
use Nybbl\AccessAcl\Contract;
use Nybbl\AccessAcl\Provider;
use Zend\Router\Http\Literal;
use Nybbl\AccessAcl\Controller;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'access.acl.not-authorised' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/not-authorised',
                    'defaults' => [
                        'controller' => Controller\NotAuthorisedController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\NotAuthorisedController::class => InvokableFactory::class,
        ],
    ],

    'access_manager' => [
        'redirect_route_name' => 'application.home',
        'default_access_all_role' => 'Guest',

        'resources' => [
            Controller\NotAuthorisedController::class => [
                [
                    'allow'   => 'Guest',
                    'actions' => ['index'],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'factories' => [
            Service\AclService::class => Service\Factory\AclServiceFactory::class,
            Event\DispatchEvent::class => Event\Factory\DispatchEventFactory::class,
            Service\AccessService::class => Service\Factory\AccessServiceFactory::class,
            Provider\DoctrineRoleProvider::class => Provider\Factory\DoctrineRoleProviderFactory::class,
        ],

        // By default, we want to use the DoctrineRoleProvider
        'aliases' => [
            Contract\RoleProviderInterface::class => Provider\DoctrineRoleProvider::class,
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'doctrine' => [
        'driver' => [
            'access_acl_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],

            'orm_default' => [
                'drivers' => [
                    'Nybbl\AccessAcl\Entity' => 'access_acl_driver',
                ],
            ],
        ],
    ],
];