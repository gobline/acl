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
 * A rule specifies a permit or deny rule and is applied to a role, resource and privilege.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Rule
{
    private $role;
    private $resource;
    private $privilege;
    private $isAllowed;

    /**
     * @param Role              $role
     * @param ResourceInterface $resource
     * @param Privilege         $privilege
     * @param bool              $isAllowed
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Role $role, ResourceInterface $resource, Privilege $privilege, $isAllowed)
    {
        if (!is_bool($isAllowed)) {
            throw new \InvalidArgumentException('$isAllowed is expected to be of type bool');
        }

        $this->role = $role;
        $this->resource = $resource;
        $this->privilege = $privilege;
        $this->isAllowed = $isAllowed;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return Privilege
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @return bool
     */
    public function isAllowed()
    {
        return $this->isAllowed;
    }
}
