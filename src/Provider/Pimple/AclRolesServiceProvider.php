<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Acl\Provider\Pimple;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Mendo\Acl\Roles;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class AclRolesServiceProvider implements ServiceProviderInterface
{
    private $reference;

    public function __construct($reference = 'acl.roles')
    {
        $this->reference = $reference;
    }

    public function register(Container $container)
    {
        $container[$this->reference.'.definitions'] = [
            'roles' => [],
        ];

        $container[$this->reference] = function ($c) {
            $roles = new Roles();
            foreach ($c[$this->reference.'.definitions']['roles'] as $role => $inherits) {
                $roles->add($role, $inherits);
            }

            return $roles;
        };
    }
}
