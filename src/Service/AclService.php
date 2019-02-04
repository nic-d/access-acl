<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:13
 */

namespace Nybbl\AccessAcl\Service;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Nybbl\AccessAcl\Contract\RoleInterface;
use Zend\Permissions\Acl\Resource\GenericResource;
use Nybbl\AccessAcl\Contract\RoleProviderInterface;

/**
 * Class AclService
 * @package Nybbl\AccessAcl\Service
 */
class AclService extends Acl
{
    /** @var array $moduleConfig */
    private $moduleConfig = [];

    /** @var RoleProviderInterface $roleProvider */
    private $roleProvider;

    /** @var array $sortedRoles */
    private $sortedRoles = [];

    /**
     * AclService constructor.
     * @param RoleProviderInterface $roleProvider
     * @param array $moduleConfig
     */
    public function __construct(RoleProviderInterface $roleProvider, array $moduleConfig = [])
    {
        $this->moduleConfig = $moduleConfig;
        $this->roleProvider = $roleProvider;
        $this->init();
    }

    /**
     * Adds roles and resources to the ACL.
     */
    public function init()
    {
        $this->defineRoles();
        $this->defineResources();
    }

    /**
     * Adds resources from module configs to the ACL.
     */
    private function defineResources()
    {
        /** @var array $resources */
        $resources = $this->moduleConfig['resources'];

        foreach ($resources as $resource => $rules) {
            $this->addResource(new GenericResource($resource));

            foreach ($rules as $rule) {
                $this->allow($rule['allow'], $resource, $rule['actions']);
            }
        }
    }

    /**
     * Adds roles from configured provider to the ACL.
     */
    private function defineRoles()
    {
        /** @var array $roles */
        $roles = $this->roleProvider->getRoles();
        $sortedRoles = $this->sortRoles($roles);

        /** @var RoleInterface $sortedRole */
        foreach ($sortedRoles as $sortedRole) {
            if ($sortedRole->hasParents()) {
                $this->addRole(
                    new GenericRole($sortedRole),
                    $this->mapParentsToArray($sortedRole->getParents())
                );

                continue;
            }

            $this->addRole(new GenericRole($sortedRole->getName()));
        }
    }

    /**
     * @param array $roles
     * @return array
     */
    private function sortRoles(array $roles): array
    {
        /** @var RoleInterface $role */
        foreach ($roles as $key => $role) {
            if ($role->hasParents()) {
                $this->sortRoles($role->getParents());
            }

            $this->sortedRoles[$role->getName()] = $role;
        }

        return $this->sortedRoles;
    }

    /**
     * @param array $parents
     * @return array
     */
    private function mapParentsToArray(array $parents): array
    {
        /** @var RoleInterface $parent */
        foreach ($parents as &$parent) {
            $parent = $parent->getName();
        }

        return $parents;
    }
}