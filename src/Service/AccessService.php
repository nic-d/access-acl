<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:04
 */

namespace Nybbl\AccessAcl\Service;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Nybbl\AccessAcl\Contract\AccessInterface;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerAwareInterface;
use Nybbl\AccessAcl\Contract\DynamicAssertionInterface;

/**
 * Class AccessService
 * @package Nybbl\AccessAcl\Service
 */
class AccessService implements EventManagerAwareInterface, AccessInterface
{
    /** @var EventManagerInterface $eventManager */
    private $eventManager;

    /** @var ContainerInterface $container */
    private $container;

    /** @var AuthenticationService $authenticationService */
    private $authenticationService;

    /** @var AclService $aclService */
    private $aclService;

    /** @var array $moduleConfig */
    private $moduleConfig = [];

    /**
     * AccessService constructor.
     * @param EventManagerInterface $eventManager
     * @param ContainerInterface $container
     * @param AuthenticationService $authenticationService
     * @param AclService $aclService
     * @param array $moduleConfig
     */
    public function __construct(
        EventManagerInterface $eventManager,
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AclService $aclService,
        array $moduleConfig = []
    )
    {
        $this->moduleConfig = $moduleConfig;
        $this->eventManager = $eventManager;
        $this->aclService = $aclService;
        $this->authenticationService = $authenticationService;
        $this->container = $container;
    }

    /**
     * @param string $controller
     * @param string $action
     * @return int
     */
    public function run(string $controller, string $action): int
    {
        /** @var array $resources */
        $resources = $this->moduleConfig['resources'];

        /** @var string $defaultRole */
        $defaultRole = $this->getCurrentRole();

        // First thing we want to do is run the dynamic assertions
        $assertionResult = $this->runAssertions(
            $this->moduleConfig['assertions'],
            $controller,
            $action,
            $defaultRole
        );

        if (!is_null($assertionResult)) {
            return $assertionResult;
        }

        // If the $controller isn't mapped yet, don't give access
        if (!isset($resources[$controller])) {
            return self::ACCESS_DENIED;
        }

        /** @var array $items */
        $items = $resources[$controller];

        foreach ($items as $item) {
            $actions = $item['actions'];
            $allows  = $item['allow'];

            if (!is_array($actions)) {
                throw new \RuntimeException(sprintf('Expected resource actions to be array, got %s', gettype($actions)));
            }

            // We need to check if the $action is in our array here
            if (!in_array($action, $actions)) {
                continue;
            }

            // If $allows matches the specified $defaultRole then lets allow
            if ($allows === $defaultRole) {
                return self::ACCESS_GRANTED;
            }

            // If there's no identity in the authentication service, deny
            if (!$this->authenticationService->hasIdentity()) {
                return self::AUTH_REQUIRED;
            }

            /** @var string $role */
            $role = $this->authenticationService->getIdentity()->getRole()->getName();

            // Now let's check with the ACL
            if (!$this->aclService->isAllowed($role, $controller, $action)) {
                return self::ACCESS_DENIED;
            }

            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;
    }

    /**
     * @param DynamicAssertionInterface $assertionInstance
     * @param string $resource
     * @param string $action
     * @param null $privilege
     * @param array $options
     * @return int|void
     */
    private function runAssertion(
        DynamicAssertionInterface $assertionInstance,
        string $resource,
        string $action,
        $privilege = null,
        array $options = []
    )
    {
        /** @var int|void $assertionResult */
        $assertionResult = $assertionInstance->assert($resource, $privilege, array_merge_recursive(
            ['action' => $action], $options)
        );

        // If the $assertionResult returns void, then its type is NULL
        if (is_null($assertionResult)) {
            return;
        }

        return $assertionResult;
    }

    /**
     * @param array|[] $assertions
     * @param string $resource
     * @param string $action
     * @param null $privilege
     * @param array $options
     * @return int|void
     */
    public function runAssertions(array $assertions = [], string $resource, string $action, $privilege = null, array $options = [])
    {
        // If no assertions are passed in, load them from the config
        if (empty($assertions)) {
            $assertions = $this->moduleConfig['assertions'];
        }

        // Iterate through all the assertions, and get the instance from the Container
        foreach ($assertions as $assertion) {
            /** @var DynamicAssertionInterface $assertionInstance */
            $assertionInstance = $this->container->get($assertion);

            /** @var int|void $assertionResult */
            $assertionResult = $this->runAssertion(
                $assertionInstance,
                $resource,
                $action,
                $privilege,
                $options
            );

            if (is_null($assertionResult)) {
                continue;
            }

            return $assertionResult;
        }
    }

    /**
     * @return string
     */
    private function getCurrentRole(): string
    {
        /** @var string $defaultRole */
        $defaultRole = $this->moduleConfig['default_access_all_role'];

        // If there's an identity in the authservice, fetch the role name
        if ($this->authenticationService->hasIdentity()) {
            return $this->authenticationService->getIdentity()->getRole()->getName();
        }

        return $defaultRole;
    }

    /**
     * @param EventManagerInterface $eventManager
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
}