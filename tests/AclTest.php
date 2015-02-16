<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Mendo\Acl\Acl;
use Mendo\Acl\Role;
use Mendo\Acl\Roles;
use Mendo\Acl\Resource;
use Mendo\Acl\ResourceInterface;
use Mendo\Acl\Matcher\StartsWithMatcher;
use Mendo\Acl\Matcher\RegexMatcher;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class AclTest extends PHPUnit_Framework_TestCase
{
    private $acl;

    public function setUp()
    {
        $this->acl = new Acl();
    }

    public function testAclAddRoleAndRetrieveIt()
    {
        $roleGuest = new Role('guest');

        $this->acl->addRole($roleGuest);

        $this->assertTrue($this->acl->hasRole($roleGuest));
        $this->assertSame($roleGuest, $this->acl->getRole($roleGuest));
    }

    public function testAclAddRoleAndRetrieveItByString()
    {
        $this->acl->addRole('guest');

        $this->assertTrue($this->acl->hasRole('guest'));
        $this->assertSame('guest', $this->acl->getRole('guest')->getName());
    }

    public function testAclGetRoleNonExistent()
    {
        $this->setExpectedException('\InvalidArgumentException', 'not found');
        $role = $this->acl->getRole('guest');
    }

    public function testAclAddInvalidRole()
    {
        $this->setExpectedException('\InvalidArgumentException', 'type');
        $role = $this->acl->addRole(new \stdClass());
    }

    public function testAclAddEmptyRole()
    {
        $this->setExpectedException('\InvalidArgumentException', 'empty');
        $role = $this->acl->addRole('');
    }

    public function testNewEmptyRole()
    {
        $this->setExpectedException('\InvalidArgumentException', 'empty');
        new Role('');
    }

    public function testAclAddRoleDuplicate()
    {
        $this->setExpectedException('\InvalidArgumentException', 'already exists');
        $this->acl->addRole('guest')
            ->addRole('guest');
    }

    public function testRoleInheritsParents()
    {
        $roleGuest = new Role('guest');
        $roleMember = new Role('member');
        $roleAdmin = new Role('admin');

        $roleMember->addParent($roleGuest);
        $roleAdmin->addParent($roleMember);

        $this->assertTrue($roleMember->inherits($roleGuest)); // parent
        $this->assertTrue($roleMember->inherits('guest'));
        $this->assertTrue($roleAdmin->inherits($roleMember));
        $this->assertTrue($roleAdmin->inherits('member'));
        $this->assertTrue($roleAdmin->inherits($roleGuest)); // ancestor
        $this->assertTrue($roleAdmin->inherits('guest'));
        $this->assertFalse($roleMember->inherits($roleMember));
        $this->assertFalse($roleGuest->inherits($roleMember));
    }

    public function testAclAddRoleWithParents()
    {
        $role = $this->acl->addRole('guest')
                    ->addRole('member', 'guest')
                    ->getRole('member');

        $this->assertTrue($role->inherits('guest'));
        $this->assertFalse($role->inherits('foo'));
    }

    public function testAclIsAllowed()
    {
        $this->acl->addRole('guest')
            ->addRole('member', 'guest')
            ->addRole('moderator', 'member')
            ->addRole('admin', 'moderator')
            ->addRole('superadmin', 'admin');

        $this->acl->allow('guest', 'page', 'view')
            ->allow('member', 'page', ['create', 'edit'])
            ->deny('moderator', 'page', 'create') // moderator cannot create a new page
            ->allow('moderator', 'page', 'delete') // moderator can edit and delete a page
            ->allow('admin', 'page', '*') // admin has all privileges on pages
            ->allow('superadmin', '*', '*'); // superadmin has all privileges on all resources

        $this->assertTrue($this->acl->isAllowed('guest', 'page', 'view'));
        $this->assertFalse($this->acl->isAllowed('guest', 'page', 'create'));
        $this->assertFalse($this->acl->isAllowed('guest', 'page', 'edit'));
        $this->assertFalse($this->acl->isAllowed('guest', 'page', 'delete'));
        $this->assertFalse($this->acl->isAllowed('guest', 'page', '*'));
        $this->assertFalse($this->acl->isAllowed('guest', 'page'));
        $this->assertFalse($this->acl->isAllowed('guest', 'users'));

        $this->assertTrue($this->acl->isAllowed('member', 'page', 'view'));
        $this->assertTrue($this->acl->isAllowed('member', 'page', 'create'));
        $this->assertTrue($this->acl->isAllowed('member', 'page', 'edit'));
        $this->assertFalse($this->acl->isAllowed('member', 'page', 'delete'));
        $this->assertFalse($this->acl->isAllowed('member', 'page', '*'));
        $this->assertFalse($this->acl->isAllowed('member', 'page'));
        $this->assertFalse($this->acl->isAllowed('member', 'users'));

        $this->assertTrue($this->acl->isAllowed('moderator', 'page', 'view'));
        $this->assertFalse($this->acl->isAllowed('moderator', 'page', 'create'));
        $this->assertTrue($this->acl->isAllowed('moderator', 'page', 'edit'));
        $this->assertTrue($this->acl->isAllowed('moderator', 'page', 'delete'));
        $this->assertFalse($this->acl->isAllowed('moderator', 'page', '*'));
        $this->assertFalse($this->acl->isAllowed('moderator', 'page'));
        $this->assertFalse($this->acl->isAllowed('moderator', 'users'));

        $this->assertTrue($this->acl->isAllowed('admin', 'page', 'view'));
        $this->assertTrue($this->acl->isAllowed('admin', 'page', 'create'));
        $this->assertTrue($this->acl->isAllowed('admin', 'page', 'edit'));
        $this->assertTrue($this->acl->isAllowed('admin', 'page', 'delete'));
        $this->assertTrue($this->acl->isAllowed('admin', 'page', '*'));
        $this->assertTrue($this->acl->isAllowed('admin', 'page'));
        $this->assertFalse($this->acl->isAllowed('admin', 'users'));

        $this->assertTrue($this->acl->isAllowed('superadmin', 'page', 'view'));
        $this->assertTrue($this->acl->isAllowed('superadmin', 'page', 'create'));
        $this->assertTrue($this->acl->isAllowed('superadmin', 'page', 'edit'));
        $this->assertTrue($this->acl->isAllowed('superadmin', 'page', 'delete'));
        $this->assertTrue($this->acl->isAllowed('superadmin', 'page', '*'));
        $this->assertTrue($this->acl->isAllowed('superadmin', 'page'));
        $this->assertTrue($this->acl->isAllowed('superadmin', 'users'));
    }

    public function testAclIsAllowedNoRuleForResource()
    {
        $this->acl->addRole('guest');

        $this->assertFalse($this->acl->isAllowed('guest', 'page', 'view'));
    }

    public function testAclAllowInvalidRole()
    {
        $this->setExpectedException('\InvalidArgumentException', 'type');
        $this->acl->allow(new \stdClass(), 'page', 'view');
    }

    public function testAclAllowEmptyRole()
    {
        $this->setExpectedException('\InvalidArgumentException', 'empty');
        $this->acl->allow('', 'page', 'view');
    }

    public function testAclAllowInvalidResource()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'type');
        $this->acl->allow('guest', new \stdClass(), 'view');
    }

    public function testAclAllowEmptyResource()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'empty');
        $this->acl->allow('guest', '', 'view');
    }

    public function testAclAllowInvalidPrivilege()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'type');
        $this->acl->allow('guest', 'page', new \stdClass());
    }

    public function testAclAllowEmptyPrivilege()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'empty');
        $this->acl->allow('guest', 'page', '');
    }

    public function testAclIsAllowedRoleInvalid()
    {
        $this->setExpectedException('\InvalidArgumentException', 'type');
        $this->acl->isAllowed(new \stdClass(), 'page', 'view');
    }

    public function testAclIsAllowedEmptyRole()
    {
        $this->setExpectedException('\InvalidArgumentException', 'empty');
        $this->acl->isAllowed('', 'page', 'view');
    }

    public function testAclIsAllowedInvalidResource()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'type');
        $this->acl->isAllowed('guest', new \stdClass(), 'view');
    }

    public function testAclIsAllowedEmptyResource()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'empty');
        $this->acl->isAllowed('guest', '', 'view');
    }

    public function testAclIsAllowedInvalidPrivilege()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'type');
        $this->acl->isAllowed('guest', 'page', new \stdClass());
    }

    public function testAclIsAllowedEmptyPrivilege()
    {
        $this->acl->addRole('guest');

        $this->setExpectedException('\InvalidArgumentException', 'empty');
        $this->acl->isAllowed('guest', 'page', '');
    }

    public function testAclAllowDeny()
    {
        $this->acl->addRole('guest');

        $this->acl->allow('guest', 'page', 'view')
            ->deny('guest', 'page', 'view');

        $this->assertFalse($this->acl->isAllowed('guest', 'page', 'view'));
    }

    public function testAclDenyAllow()
    {
        $this->acl->addRole('guest');

        $this->acl->deny('guest', 'page', 'view')
            ->allow('guest', 'page', 'view');

        $this->assertTrue($this->acl->isAllowed('guest', 'page', 'view'));
    }

    public function testAclResourceStartsWithMatcher()
    {
        $resource = new Resource('page/', new StartsWithMatcher());

        $this->acl->addRole('guest');

        $this->acl->allow('guest', $resource, '*');

        $this->assertTrue($this->acl->isAllowed('guest', 'page/view/42'));
        $this->assertFalse($this->acl->isAllowed('guest', 'user/view/42'));
    }

    public function testAclResourceRegexMatcher()
    {
        $resource = new Resource('%^page/(.*?)/view%', new RegexMatcher());

        $this->acl->addRole('guest');

        $this->acl->allow('guest', $resource, '*');

        $this->assertTrue($this->acl->isAllowed('guest', 'page/42/view'));
        $this->assertFalse($this->acl->isAllowed('guest', 'page/42/edit'));
        $this->assertFalse($this->acl->isAllowed('guest', 'user/42/edit'));
    }

    public function testCustomResource()
    {
        $resource = new MyCustomResource(5, 7);

        $this->acl->addRole('guest');

        $this->acl->allow('guest', $resource, '*');

        $this->assertTrue($this->acl->isAllowed('guest', $resource));
    }

    public function testMultipleAclOneSetOfRoles()
    {
        $roles = new Roles();
        $roles->add('guest')
              ->add('member', 'guest');

        $this->acl1 = new Acl($roles);

        $this->acl1->allow('guest', 'foo', 'view');

        $this->acl2 = new Acl($roles);

        $this->acl2->allow('guest', 'bar', 'view');

        $this->assertTrue($this->acl1->isAllowed('guest', 'foo', 'view'));
        $this->assertFalse($this->acl2->isAllowed('guest', 'foo', 'view'));

        $this->assertTrue($this->acl2->isAllowed('guest', 'bar', 'view'));
        $this->assertFalse($this->acl1->isAllowed('guest', 'bar', 'view'));
    }

    public function testAclDefaultResourceMatcher()
    {
        $resource = new Resource('page/', new StartsWithMatcher());

        $this->acl->addRole('guest');

        $this->acl->setDefaultResourceMatcher('startsWith');

        $this->acl->allow('guest', 'page/', '*');

        $this->assertTrue($this->acl->isAllowed('guest', 'page/view/42'));
        $this->assertFalse($this->acl->isAllowed('guest', 'user/view/42'));
    }
}

class MyCustomResource implements ResourceInterface
{
    private $x;
    private $y;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function matches($resource)
    {
        if (!$resource instanceof MyCustomResource) {
            return false;
        }

        return $this->x === $resource->x && $this->y === $resource->y;
    }
}
