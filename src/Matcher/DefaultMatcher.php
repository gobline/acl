<?php

/*
 * Gobline Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gobline\Acl\Matcher;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class DefaultMatcher implements MatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function matches($a, $b)
    {
        return $a === $b;
    }
}
