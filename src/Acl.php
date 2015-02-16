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

use Matcher\MatcherInterface;

/**
 * An ACL implementation.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Acl implements AclInterface
{
    private $roles;
    private $rules = [];
    private $defaultResourceMatcher;

    /**
     * @param Roles $roles
     */
    public function __construct(Roles $roles = null)
    {
        $this->roles = $roles ?: new Roles();
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role, $inherits = [])
    {
        $this->roles->add($role, $inherits);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole($role)
    {
        return $this->roles->get($role);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return $this->roles->has($role);
    }

    /**
     * {@inheritdoc}
     */
    public function allow($role, $resource, $privileges)
    {
        return $this->addRule($role, $resource, $privileges, true);
    }

    /**
     * {@inheritdoc}
     */
    public function deny($role, $resource, $privileges)
    {
        return $this->addRule($role, $resource, $privileges, false);
    }

    /**
     * @param Role|string              $role
     * @param ResourceInterface|string $resource
     * @param Privilege|string|array   $privileges
     * @param bool                     $isAllowed
     *
     * @throws \InvalidArgumentException
     *
     * @return Acl
     */
    private function addRule($role, $resource, $privileges, $isAllowed)
    {
        $role = $this->roles->get($role);

        if (is_scalar($resource)) {
            $resource = new Resource($resource, $this->defaultResourceMatcher);
        } elseif (!$resource instanceof ResourceInterface) {
            throw new \InvalidArgumentException('$resource is expected to be of type string or Mendo\Acl\ResourceInterface');
        }

        if (!is_array($privileges)) {
            $privileges = [$privileges];
        }
        foreach ($privileges as $privilege) {
            if (is_scalar($privilege)) {
                $privilege = new Privilege($privilege);
            } elseif (!$privilege instanceof Privilege) {
                throw new \InvalidArgumentException('$privileges elements are expected to be of type string or Mendo\Acl\Privilege');
            }
            $rule = new Rule($role, $resource, $privilege, $isAllowed);
            array_unshift($this->rules, $rule); // last rule added is the first to be evaluated
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($role, $resource, $privilege = '*')
    {
        $rule = $this->getRule($role, $resource, $privilege);

        return ($rule) ? $rule->isAllowed() : false;
    }

    /**
     * @param Role|string              $role
     * @param ResourceInterface|string $resource
     * @param Privilege|string         $privileges
     *
     * @throws \InvalidArgumentException
     *
     * @return Rule
     */
    private function getRule($role, $resource, $privilege)
    {
        $role = $this->roles->get($role);

        if (is_scalar($resource)) {
            $resource = new Resource($resource);
        } elseif (!$resource instanceof ResourceInterface) {
            throw new \InvalidArgumentException('$resource is expected to be of type string or Mendo\Acl\ResourceInterface');
        }

        if (is_scalar($privilege)) {
            $privilege = new Privilege($privilege);
        } elseif (!$privilege instanceof Privilege) {
            throw new \InvalidArgumentException('$privilege is expected to be of type string or Mendo\Acl\Privilege');
        }

        foreach ($this->rules as $rule) {
            if (!$rule->getRole()->equals($role)) {
                continue;
            }
            if (!$rule->getPrivilege()->equals($privilege) && !$rule->getPrivilege()->equals('*')) {
                continue;
            }
            if (!$rule->getResource()->matches($resource) && (!$rule->getResource() instanceof Resource || !$rule->getResource()->matches('*'))) {
                continue;
            }

            return $rule;
        }

        foreach ($role->getParents() as $role) {
            $rule = $this->getRule($role, $resource, $privilege);
            if ($rule !== null) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * @param MatcherInterface|string $defaultResourceMatcher
     *
     * @throws \InvalidArgumentException
     */
    public function setDefaultResourceMatcher($defaultResourceMatcher)
    {
        if (is_scalar($defaultResourceMatcher)) {
            $defaultResourceMatcher = 'Mendo\\Acl\\Matcher\\'.ucfirst($defaultResourceMatcher).'Matcher';
            $defaultResourceMatcher = new $defaultResourceMatcher();
        } elseif (!$resource instanceof MatcherInterface) {
            throw new \InvalidArgumentException('$defaultResourceMatcher is expected to be of type string or Mendo\Acl\Matcher\MatcherInterface');
        }

        $this->defaultResourceMatcher = $defaultResourceMatcher;
    }
}
