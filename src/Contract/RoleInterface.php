<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 00:38
 */

namespace Nybbl\AccessAcl\Contract;

/**
 * Interface RoleInterface
 * @package Nybbl\AccessAcl\Contract
 */
interface RoleInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name);

    /**
     * @return array
     */
    public function getParents(): array;

    /**
     * @param array $parents
     * @return void
     */
    public function setParents(array $parents);

    /**
     * @return array
     */
    public function getChildren(): array;

    /**
     * @param array $children
     * @return void
     */
    public function setChildren(array $children);

    /**
     * @return bool
     */
    public function hasParents(): bool;

    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}