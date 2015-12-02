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
class RegexMatcher implements MatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function matches($subject, $pattern)
    {
        return (bool) preg_match($pattern, $subject);
    }
}
