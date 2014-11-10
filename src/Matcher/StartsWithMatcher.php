<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Acl\Matcher;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class StartsWithMatcher implements MatcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function matches($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
