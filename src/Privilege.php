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
 * A privilege is an access right (or permission) for a resource.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Privilege
{
    protected $name;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Privilege|string $privilege
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function equals($privilege)
    {
        if (is_scalar($privilege)) {
            $privilege = new self($privilege);
        } elseif (!$privilege instanceof self) {
            throw new \InvalidArgumentException('$privilege is expected to be of type string or Gobline\Acl\Privilege');
        }

        return $privilege->getName() === $this->name;
    }
}
