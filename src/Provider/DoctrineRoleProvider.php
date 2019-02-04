<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 03/02/2019
 * Time: 23:19
 */

namespace Nybbl\AccessAcl\Provider;

use Nybbl\AccessAcl\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Nybbl\AccessAcl\Contract\RoleProviderInterface;

/**
 * Class DoctrineRoleProvider
 * @package Nybbl\AccessAcl\Provider
 */
class DoctrineRoleProvider implements RoleProviderInterface
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /**
     * DoctrineRoleProvider constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->entityManager
            ->getRepository(Role::class)
            ->findBy([], ['id' => 'asc']);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getRoleById(int $id)
    {
        return $this->entityManager
            ->getRepository(Role::class)
            ->find($id);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getRoleByName(string $name)
    {
        return $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy([
                'name' => $name,
            ]);
    }

    /**
     * @param int|string $id
     * @return array
     */
    public function getRoleParents($id): array
    {
        // Fetch the Role by id and then return parents
        if (is_int($id)) {
            return $this->getRoleById($id)->getParents();
        }

        // Fetch the Role by name and then return parents
        if (is_string($id)) {
            return $this->getRoleByName($id)->getParents();
        }

        // If an object is passed, it's likely to be an instance of Role
        if (is_object($id)) {
            return $id->getParents();
        }

        return [];
    }
}