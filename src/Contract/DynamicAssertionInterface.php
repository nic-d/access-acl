<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 14:37
 */

namespace Nybbl\AccessAcl\Contract;

/**
 * Interface DynamicAssertionInterface
 * @package Nybbl\AccessAcl\Contract
 */
interface DynamicAssertionInterface extends AccessInterface
{
    /**
     * @param string $resource
     * @param null $privilege
     * @param array $options
     * @return mixed
     */
    public function assert(string $resource, $privilege = null, array $options = []);
}