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
interface ResourceInterface
{
    /**
     * @param ResourceInterface|string $resource
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function matches($resource);
}
