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

use Gobline\Acl\Matcher\MatcherInterface;
use Gobline\Acl\Matcher\DefaultMatcher;

/**
 * A resource represents an area, operation, file or object that needs to be controlled.
 *
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Resource implements ResourceInterface
{
    private $name;
    private $matcher;

    /**
     * The $matcher parameter instance evaluates wether two resources are equal.
     * If no $matcher has been specified, a default matcher will evaluate
     * two resources as equal if their names are identical.
     *
     * When a role requests access to a resource, the ACL will try to find
     * the registered resource that best matches the requested resource.
     * The most straightforward example demonstrating the use of matchers,
     * would be the implementation of an ACL managing access rights to files.
     * For instance, if you registered a resource with a name such as
     * "/home/john/" and the $matcher being Matcher/StartsWitchMatcher,
     * a request to "/home/john/file.txt" will have the ACL check the
     * rules defined for the "/home/john/" resource.
     *
     * @param string           $name
     * @param MatcherInterface $matcher
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($name, MatcherInterface $matcher = null)
    {
        $this->name = (string) $name;
        if ($this->name === '') {
            throw new \InvalidArgumentException('$name cannot be empty');
        }
        $this->matcher = $matcher ?: new DefaultMatcher();
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
    public function matches($resource)
    {
        if (!$resource instanceof Resource) {
            if (is_scalar($resource)) {
                $resource = new Resource($resource);
            } elseif ($resource instanceof ResourceInterface) {
                return false;
            } else {
                throw new \InvalidArgumentException('$resource is expected to be of type string or Gobline\Acl\ResourceInterface');
            }
        }

        $resourceName = $resource->getName();

        return $this->matcher->matches($resourceName, $this->name);
    }
}
