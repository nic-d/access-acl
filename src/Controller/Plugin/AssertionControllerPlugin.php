<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 14:07
 */

namespace Nybbl\AccessAcl\Controller\Plugin;

use Nybbl\AccessAcl\Service\AccessService;
use Nybbl\AccessAcl\Contract\AccessInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class AssertionControllerPlugin
 * @package Nybbl\AccessAcl\Controller\Plugin
 */
class AssertionControllerPlugin extends AbstractPlugin
{
    /** @var AccessService $accessService */
    private $accessService;

    /**
     * AssertionControllerPlugin constructor.
     * @param AccessService $accessService
     */
    public function __construct(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    /**
     * @param string $resource
     * @param string $action
     * @param null $privilege
     * @param array $options
     * @return bool
     */
    public function __invoke(string $resource, string $action, $privilege = null, array $options = []): bool
    {
        /** @var int $assertionResult */
        $assertionResult = $this->accessService->runAssertions([], $resource, $action, $privilege, $options);

        if ($assertionResult === AccessInterface::ACCESS_GRANTED) {
            return true;
        }

        return false;
    }
}