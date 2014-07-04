LightCMS
========
LightCMS is a CMS (Content Manager System) which is built with Symfony 2
framework.
Well, you can say "another CMS for what ?". Actually, many CMS make many thing,
and need cpu ressource to display a website.

Features include:
- Pages are displaying like a tree, easier to find a page for edition
- Posts listing is available
- Page model is usefull for specified page development

Documentation
-------------
Coming soon

Installation
------------

Installation is not so hard

1. Download FulgurioLightCMSBundle using composer
2. Enable the Bundle
3. Import routing
4. Configure your yml files
5: Set database fixture

That's easy !

### Step 1: Download FulgurioLightCMSBundle

First, edit composer.json, and add the bundle

``` yaml
{
    "require": {
        "fulgurio/light-cms-bundle" : "dev-master"
    }
}```

After, just launch composer, it will load all dependencies

``` bash
$ ./composer update
```

### Step 2: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
        new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
        new Fulgurio\LightCMSBundle\FulgurioLightCMSBundle(),
    );
}
```

### Step 3: Import routing file

Now that you have activated and configured the bundle, all that is left to do is
 import the FulgurioLightCMSBundle routing file.

You need to put it on the bottom of your file, to be the last routes used (if no
 route parse, LightCMS try to found the page into the database)

``` yaml
fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

FulgurioLightCMSBundle:
    resource: "@FulgurioLightCMSBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 4: Configure your yml files

You need to set on the anonymous access, and to limit admin access. Edit
config/security.yml file and put the following configuration :
```yaml
    firewalls:
        secured_area:
            pattern:    ^/
            anonymous: ~
            http_basic:
                realm: "Secured Demo Area"
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

After that, you need to enable the translation (actually, only english and
french is available)
``` yaml
# app/config/config.yml

framework:
    translator: ~
```

### Step 5: Set database fixture

You need to load database schema and default data. With doctrine and
doctrine-fixture-bundle, no problem !

``` bash
$ ./bin/console doctrine:schema:update --force

$ ./bin/console doctrine:fixtures:load
```

Just before to try, don't forget to clear your cache. And if everything's
working well, you will see the homepage
