<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Acl;

/**
 * A role represents a user, users' group or object that may request access to a resource.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Role implements RoleInterface
{
    protected $name;
    protected $parents = []; // inherited roles

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
        if ($this->name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }
    }

    /**
     * @param Role|string $role
     */
    public function addParent($role)
    {
        if (!$role instanceof Role) {
            $rolename = (string) $role;
            $role = new Role($rolename);
        }

        $this->parents[$role->getName()] = $role;
    }

    /**
     * @return Role[]
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function equals($role)
    {
        if (is_scalar($role)) {
            $role = new Role($role);
        } elseif (!$role instanceof Role) {
            throw new \InvalidArgumentException('$role is expected to be of type string or Gobline\Acl\Role');
        }

        $roleName = $role->getName();

        return $roleName === $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function inherits($role)
    {
        if (!$this->parents) {
            return false;
        }

        if (is_scalar($role)) {
            $role = new Role($role);
        } elseif (!$role instanceof Role) {
            throw new \InvalidArgumentException('$role is expected to be of type string or Gobline\Acl\Role');
        }

        foreach ($this->parents as $parent) {
            if ($role->equals($parent)) {
                return true;
            }
            if ($parent->inherits($role)) {
                return true;
            }
        }

        return false;
    }
}
