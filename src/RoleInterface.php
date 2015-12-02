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
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
interface RoleInterface
{
    /**
     * @param Role|string $role
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function equals($role);

    /**
     * @param Role|string $role
     *
     * @return bool
     */
    public function inherits($role);
}
