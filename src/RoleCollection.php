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
 * Registry of roles.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class RoleCollection
{
    private $roles = [];

    /**
     * @param Role|string $role
     * @param array       $inherits
     *
     * @throws \InvalidArgumentException
     *
     * @return Roles
     */
    public function add($role, $inherits = [])
    {
        if (is_scalar($role)) {
            $role = new Role($role);
        } elseif (!$role instanceof Role) {
            throw new \InvalidArgumentException('$role is expected to be of type string or Gobline\Acl\Role');
        }

        $roleName = $role->getName();

        if ($this->has($roleName)) {
            throw new \InvalidArgumentException('Role "'.$roleName.'" already exists');
        }

        if (!is_array($inherits)) {
            $inherits = [$inherits];
        }
        foreach ($inherits as $parent) {
            if (is_scalar($parent)) {
                $parent = $this->get($parent);
            } elseif (!$parent instanceof Role) {
                throw new \InvalidArgumentException('$inherits elements are expected to be of type string or Gobline\Acl\Role');
            }
            $role->addParent($parent);
        }

        $this->roles[$roleName] = $role;

        return $this;
    }

    /**
     * @param Role|string $role
     *
     * @throws \InvalidArgumentException
     *
     * @return Role
     */
    public function get($role)
    {
        if (is_scalar($role)) {
            $role = new Role($role);
        } elseif (!$role instanceof Role) {
            throw new \InvalidArgumentException('$role is expected to be of type string or Gobline\Acl\Role');
        }

        $roleName = $role->getName();

        if (!$this->has($role)) {
            throw new \InvalidArgumentException('Role "'.$roleName.'" not found');
        }

        return $this->roles[$roleName];
    }

    /**
     * @param Role|string $role
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function has($role)
    {
        if (is_scalar($role)) {
            $role = new Role($role);
        } elseif (!$role instanceof Role) {
            throw new \InvalidArgumentException('$role is expected to be of type string or Gobline\Acl\Role');
        }

        $roleName = $role->getName();

        return isset($this->roles[$roleName]);
    }

    public function setCollection(array $collection)
    {
        foreach ($collection as $role => $inherits) {
            $this->add($role, $inherits);
        }

        return $this;
    }
}
