<?php
/**
 * Created by PhpStorm.
 * User: Nic
 * Date: 04/02/2019
 * Time: 00:38
 */

namespace Nybbl\AccessAcl\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nybbl\AccessAcl\Contract\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="access_acl_role")
 *
 * Class Role
 * @package Nybbl\AccessAcl\Entity
 */
class Role implements RoleInterface
{
    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
    }
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="AccessAcl\Entity\Role", inversedBy="children", cascade={"all"})
     * @ORM\JoinTable(name="access_acl_role_hierarchy",
     *      joinColumns={@ORM\JoinColumn(name="child_role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent_role_id", referencedColumnName="id")}
     * )
     */
    private $parents;

    /**
     * @ORM\ManyToMany(targetEntity="AccessAcl\Entity\Role", mappedBy="parents", cascade={"all"})
     */
    private $children;

    # ---------------------------------------------------------------
    # - GETTERS AND SETTERS
    # ---------------------------------------------------------------

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getParents(): array
    {
        return $this->parents->toArray();
    }

    /**
     * @param array $parents
     */
    public function setParents(array $parents)
    {
        $this->parents = new ArrayCollection($parents);
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children->toArray();
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children)
    {
        $this->children = new ArrayCollection($children);
    }

    /**
     * @return bool
     */
    public function hasParents(): bool
    {
        return !$this->parents->isEmpty();
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}