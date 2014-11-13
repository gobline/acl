# ACL (Access Control List) Component - Mendo Framework

ACL's allow an application to control access to its protected areas, files, operations and objects from requests.

* a **resource** represents an area or element to which access is controlled.
* a **role** represents a user, users' group or object that may request access to a resource.
* a **privilege** is an access right (or permission) for a resource, such as read and write permissions to a file.

## Why Another ACL Component?

The Mendo ACL component was primarily written to **add support for** defining rules on **path resources**.
You will find the basic usage in the following sections but by then, if you are already a little familiar to ACL, you can already have a look at the quick excerpt below demonstrating the utility and advantage of the component.

```php
$memberArea = new Mendo\Acl\Resource('/member-area', new Mendo\Acl\Matcher\StartsWithMatcher());

$acl->addRole('member')
	->allow('member', $memberArea, '*');
    
$adminArea = new Mendo\Acl\Resource('/admin-area', new Mendo\Acl\Matcher\StartsWithMatcher());

$acl->addRole('admin')
	->allow('admin', $adminArea, '*');

$acl->isAllowed('guest', '/member-area/edit/profile'); // returns false
$acl->isAllowed('member', '/member-area/edit/profile'); // returns true
$acl->isAllowed('member', '/admin-area/user/list'); // returns false
```

In a web application, this is particularly useful to control access on the application's areas through the URL path.

## Creating an ACL

```php
$acl = new Mendo\Acl\Acl();
```

## Adding Roles to the ACL

```php
$acl->addRole('guest');
```

or

```php
$roleGuest = new Mendo\Acl\Role('guest');
$acl->addRole($roleGuest);
```

## Defining Access Controls

After adding the relevant roles, rules can be established that define how resources may be accessed by roles.

```php
$acl->allow('guest', 'page', 'view'); // the role "guest" is now allowed to "view" the "page" resource
```

```php
$acl->allow('member', 'page', ['view', 'create', 'edit']); // the role "member" is allowed to "view", "create" and "edit" the "page" resource
```

To define a rule applied to all resources, the special resource named "*" can be used:

```php
$acl->allow('guest', '*', 'view'); // the role "guest" is now allowed to "view" any resource
```

To define a rule with all privileges, the special privilege named "*" can be used:

```php
$acl->allow('admin', 'page', '*'); // the role "admin" is now allowed to access any privilege on the "page" resource
```

```php
$acl->allow('superadmin', '*', '*'); // the role "superadmin" is now allowed to access any privilege on any resource
```

## Querying the ACL

After adding the rules, we can query the ACL to check if a role has been given permission or not.

```php
$acl->isAllowed('guest', 'page', 'edit'); // returns false

$acl->isAllowed('admin', 'page', 'edit'); // returns true

$acl->isAllowed('admin', 'user', 'edit'); // returns false

$acl->isAllowed('superadmin', 'user', 'edit'); // returns true
```

You will note that by default, until a developer specifies an *allow* rule, ```Mendo\Acl\Acl``` denies access to every privilege upon every resource by every role.

## Resources

As you might have noticed in the examples above, the resources are registered when defining the rules, while the roles must have previously been added to the ACL.

The reason for this is because the resources can not only just be a name or identifier, but also a pattern or regex, or even a custom object implementing the ```matches()``` method of the ```Mendo\Acl\ResourceInterface``` interface.

The most straightforward example demonstrating the use of matchers, would be implementing an ACL managing access rights to files.

```php
$resource = new Mendo\Acl\Resource('/home/john', new Mendo\Acl\Matcher\StartsWithMatcher());

$acl->addRole('john')
	->allow('john', $resource, 'read');

$acl->isAllowed('john', '/home/john/file.txt', 'read'); // returns true
$acl->isAllowed('john', '/home/john/file.txt', 'write'); // returns false
$acl->isAllowed('john', '/home/matthew/file.txt', 'read'); // returns false
```

Another example:

```php
$resource = new Mendo\Acl\Resource('%^/page/(.*?)/view%', new RegexMatcher());

$acl->addRole('guest')
	->allow('guest', $resource, '*');

$acl->isAllowed('guest', '/page/42/view')); // returns true
$acl->isAllowed('guest', '/page/42/edit')); // returns false
```

## Roles Inheritance

Roles can inherit from other roles, and consequently inherit their rules.

```php
$acl->addRole('guest')
  ->addRole('member', 'guest') // "member" inherits the rules of "guest"
  ->addRole('moderator', 'member'); // "moderator" inherits the rules of "member"
  
$acl->allow('guest', 'page', 'view') // guests can only view pages
  ->allow('member', 'page', ['create', 'edit']) // members can view, create and edit pages
  ->deny('moderator', 'page', 'create') // moderators cannot create a new page
  ->allow('moderator', 'page', 'delete'); // moderators can view, edit and delete pages
```

The example above also demonstrates the use of ```deny()``` (because one might wonder what is the purpose of having a ```deny()``` method if anything is denied by default anyway). The moderators inherit the *view* privilege from the *guest* role and the *create* and *edit* privileges from the *member* role. However, we don't want to allow a moderator to be able to create new pages, but only moderate existing pages. To achieve this, we simply add a deny rule overriding the inherited rule that granted *create* persmission, as shown above.

## Sharing Roles among Multiple ACL Instances

There are cases where you might need to have multiple ACL instances. For instance, you might need to define rules for path resources, and rules for different resources in your application. To avoid mixing different type of resources in your ACL, you can create multiple ACL instances and share a unique *role registry*.

```php
$roles = new Mendo\Acl\Roles(); // registry of roles

$roles->add('guest')
  ->add('member', 'guest')
  ->add('moderator', 'member');

$acl1 = new Mendo\Acl\Acl($roles);

// defining rules for $acl1

$acl2 = new Mendo\Acl\Acl($roles);

// defining rules for $acl2
```

## Installation

You can install Mendo ACL using the dependency management tool [Composer](https://getcomposer.org/).
Run the *require* command to resolve and download the dependencies:

```
composer require mendoframework/acl
```