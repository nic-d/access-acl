<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:01
 */

namespace Nybbl\AccessAcl;

use Zend\EventManager\EventInterface;
use Nybbl\AccessAcl\Event\DispatchEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;

/**
 * Class Module
 * @package Nybbl\AccessAcl
 */
class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    const VERSION = '1.0.0';

    /**
     * @return array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param EventInterface $e
     * @return array|void
     */
    public function onBootstrap(EventInterface $e)
    {
        // Don't attach the DispatchEvent if we're running in the Console
        if (php_sapi_name() === 'cli' || php_sapi_name() === 'cli-server') {
            return;
        }

        /** @var EventManagerInterface $eventManager */
        $eventManager = $e->getApplication()->getEventManager();

        /** @var DispatchEvent $dispatchEvent */
        $dispatchEvent = $e->getApplication()->getServiceManager()->get(DispatchEvent::class);
        $dispatchEvent->attach($eventManager, 900);
    }
}