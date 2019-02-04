<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:18
 */

namespace Nybbl\AccessAcl\Contract;

/**
 * Interface RoleProviderInterface
 * @package Nybbl\AccessAcl\Contract
 */
interface RoleProviderInterface
{
    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @param int $id
     * @return mixed
     */
    public function getRoleById(int $id);

    /**
     * @param string $name
     * @return mixed
     */
    public function getRoleByName(string $name);

    /**
     * @param string|int $id
     * @return array
     */
    public function getRoleParents($id): array;
}