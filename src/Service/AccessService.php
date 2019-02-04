<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:04
 */

namespace Nybbl\AccessAcl\Service;

use Zend\EventManager\EventManagerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Class AccessService
 * @package Nybbl\AccessAcl\Service
 */
class AccessService implements EventManagerAwareInterface
{
    /** @var EventManagerInterface $eventManager */
    private $eventManager;

    /** @var AuthenticationService $authenticationService */
    private $authenticationService;

    /** @var AclService $aclService */
    private $aclService;

    /** @var array $moduleConfig */
    private $moduleConfig = [];

    const ACCESS_GRANTED = 1;
    const AUTH_REQUIRED  = 2;
    const ACCESS_DENIED  = 3;

    /**
     * AccessService constructor.
     * @param EventManagerInterface $eventManager
     * @param AuthenticationService $authenticationService
     * @param AclService $aclService
     * @param array $moduleConfig
     */
    public function __construct(
        EventManagerInterface $eventManager,
        AuthenticationService $authenticationService,
        AclService $aclService,
        array $moduleConfig = []
    )
    {
        $this->moduleConfig = $moduleConfig;
        $this->eventManager = $eventManager;
        $this->aclService = $aclService;
        $this->authenticationService = $authenticationService;
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
        $defaultRole = $this->moduleConfig['default_access_all_role'];

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