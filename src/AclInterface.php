<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Acl;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface AclInterface
{
    /**
     * @param Role|string       $role
     * @param Role|string|array $inherits
     *
     * @throws \InvalidArgumentException
     *
     * @return AclInterface
     */
    public function addRole($role, $inherits = []);

    /**
     * @param Role|string $role
     *
     * @throws \InvalidArgumentException
     *
     * @return Role
     */
    public function getRole($role);

    /**
     * @param Role|string $role
     *
     * @throws \InvalidArgumentException
     *
     * @return Role
     */
    public function hasRole($role);

    /**
     * @param Role|string              $role
     * @param ResourceInterface|string $resource
     * @param Privilege|string|array   $privileges
     *
     * @throws \InvalidArgumentException
     *
     * @return AclInterface
     */
    public function allow($role, $resource, $privileges);

    /**
     * @param Role|string              $role
     * @param ResourceInterface|string $resource
     * @param Privilege|string|array   $privileges
     *
     * @throws \InvalidArgumentException
     *
     * @return AclInterface
     */
    public function deny($role, $resource, $privileges);

    /**
     * @param Role|string              $role
     * @param ResourceInterface|string $resource
     * @param Privilege|string         $privileges
     *
     * @throws \InvalidArgumentException
     *
     * @return AclInterface
     */
    public function isAllowed($role, $resource, $privilege = '*');
}
