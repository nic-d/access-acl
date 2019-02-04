<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:38
 */

namespace Nybbl\AccessAcl\Event;

use Zend\Uri\Uri;
use Zend\Mvc\MvcEvent;
use Nybbl\AccessAcl\Service\AccessService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class DispatchEvent
 * @package Nybbl\AccessAcl\Event
 */
class DispatchEvent implements ListenerAggregateInterface
{
    /** @var array $listeners */
    private $listeners = [];

    /** @var AccessService $accessService */
    private $accessService;

    /** @var array $config */
    private $config;

    /**
     * DispatchEvent constructor.
     * @param AccessService $accessService
     * @param array $config
     */
    public function __construct(AccessService $accessService, array $config = [])
    {
        $this->accessService = $accessService;
        $this->config = $config;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->getSharedManager()->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            $priority
        );
    }

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $key => $value) {
            if ($events->detach($value)) {
                unset($this->listeners[$key]);
            }
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function onDispatch(MvcEvent $event)
    {
        /** @var string $redirectRouteName */
        $redirectRouteName = $this->config['redirect_route_name'];

        // Get the controller, action and the HTTP request method
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        // Run the filter on this request
        $result = $this->accessService->run($controllerName, $actionName);

        if ($result === AccessService::AUTH_REQUIRED) {
            /** @var Uri $uri */
            $uri = $event->getApplication()->getRequest()->getUri();

            $uri
                ->setScheme(null)
                ->setHost(null)
                ->setPort(null)
                ->setUserInfo(null);

            // Get the redirect URI so we can append to the query params
            $redirectUrl = $uri->toString();

            return $controller->redirect()->toRoute(
                $redirectRouteName,
                [],
                ['query' => ['redirectUrl' => $redirectUrl]]
            );
        } elseif ($result === AccessService::ACCESS_DENIED) {
            return $controller->redirect()->toRoute($redirectRouteName);
        }

        return;
    }
}