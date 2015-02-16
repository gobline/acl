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
use Mendo\Acl\Acl;
use Mendo\Acl\Roles;
use Mendo\Acl\Resource;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class AclServiceProvider implements ServiceProviderInterface
{
    private $reference;

    public function __construct($reference = 'acl')
    {
        $this->reference = $reference;
    }

    public function register(Container $container)
    {
        $container[$this->reference.'.definitions'] = [
            'roles' => [],
            'resources' => [],
        ];

        $container[$this->reference] = function ($c) {
            if (!empty($c[$this->reference.'.acl.roles'])) {
                if (empty($c[$c[$this->reference.'.acl.roles']])) {
                    throw new \Exception('Dependency "'.$this->reference.'.acl.roles" not found');
                }
                $roles = $c[$c[$this->reference.'.acl.roles']];
            } else {
                $roles = new Roles();
                foreach ($c[$this->reference.'.definitions']['roles'] as $role => $inherits) {
                    $roles->add($role, $inherits);
                }
            }

            $acl = new Acl($roles);
            if (!empty($c[$this->reference.'.defaultResourceMatcher'])) {
                $acl->setDefaultResourceMatcher($c[$this->reference.'.defaultResourceMatcher']);
            }

            foreach ($c[$this->reference.'.definitions']['resources'] as $resource => $data) {
                $rules = !empty($data['rules']) ? $data['rules'] : [];
                foreach ($rules as $rule) {
                    if (empty($rule['role'])) {
                        throw new \Exception('role not specified');
                    }
                    $role = $rule['role'];
                    $privileges = !empty($rule['privileges']) ? $rule['privileges'] : '*';
                    $allowed = !empty($rule['allowed']) ? $rule['allowed'] : true;
                    if ($allowed) {
                        $acl->allow($role, $resource, $privileges);
                    } else {
                        $acl->deny($role, $resource, $privileges);
                    }
                }
            }

            return $acl;
        };
    }
}
