INSTALLATION
------------

Installation is not so hard

1. Download FulgurioLightCMSBundle using composer
2. Enable the Bundle
3. Import routing
4. Configure your yml files
5. Set database fixture

That's easy !

### Step 1: Download FulgurioLightCMSBundle

First, edit composer.json, and add the bundle

``` json
{
    "require": {
        "fulgurio/light-cms-bundle" : "dev-master"
    }
}
```

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
        new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
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
# app/config/routing.yml
fos_js_routing:
    resource: "@FOSJsRoutingBundle/Resources/config/routing/routing.xml"

FulgurioLightCMSBundle:
    resource: "@FulgurioLightCMSBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 4: Configure your yml files

You need to set on the anonymous access, and to limit admin access. Edit
config/security.yml file and put the following configuration :
``` yaml
# app/config/security.yml
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

and doctrine filters
``` yaml
doctrine:
    orm:
// ...
        filters:
            page:
                class: Fulgurio\LightCMSBundle\Filter\PageFilter
                enabled: false
```

and the gedmo extensions :
``` yaml
stof_doctrine_extensions:
    orm:
        default:
            timestampable: true
```

### Step 5: Set database fixture

You need to load database schema and default data. With doctrine and
doctrine-fixture-bundle, no problem !

``` bash
$ ./app/console doctrine:schema:update --force

$ ./app/console doctrine:fixtures:load
```

Just before to try, don't forget to clear your cache. And if everything's
working well, you will see the homepage
