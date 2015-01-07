USER ADMIN ACCESS
========

Symfony has a security component to restrict page access. You can use the
HTTP authenfication, or a html form. You can get more information on the
official [website](http://symfony.com/doc/current/book/security.html)

Here's the configuration to use for Light CMS

``` yaml
# app/config/security.yml
security:
    encoders:
        Symfony\Component\Security\Core\User\UserInterface: plaintext
// ...
    providers:
        lightcms:
            entity:
                class: Fulgurio\LightCMSBundle\Entity\User
                property: username
// ...
    firewalls:
        lightcms_secured_area:
            form_login:
                provider:   lightcms
                login_path: /login
                check_path: /login_check
            logout:
                path:       /logout
                target:     /
            anonymous:      ~
// ...
    access_control:
        - { path: ^/admin/, roles: ROLE_ADMIN }
```

You'll see a login form on /admin access.

Now, you need to add your first user. To do that, there's a command with the
console :
``` bash
$ ./app/console user:add

user:add username password email [role]
```

Just type your login informations, like :
``` bash
./app/console user:add admin mypassword admin@foo.bar ROLE_ADMIN
```

Note: you need to use the role which is allowed to access to /admin, in this
document, it's ROLE_ADMIN. Actually, roles are unused, there's no difference
between ROLE_USER or ROLE_ADMIN.