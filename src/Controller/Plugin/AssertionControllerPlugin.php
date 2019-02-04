<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 14:07
 */

namespace Nybbl\AccessAcl\Controller\Plugin;

use Nybbl\AccessAcl\Service\AclService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class AssertionControllerPlugin
 * @package Nybbl\AccessAcl\Controller\Plugin
 */
class AssertionControllerPlugin extends AbstractPlugin
{
    /** @var AclService $aclService */
    private $aclService;

    /**
     * AssertionControllerPlugin constructor.
     * @param AclService $aclService
     */
    public function __construct(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    /**
     * @param string $role
     * @param string $resource
     * @param null $privilege
     * @return bool
     */
    public function __invoke(string $role, string $resource, $privilege = null): bool
    {
        return $this->aclService->isAllowed($role, $resource, $privilege);
    }
}