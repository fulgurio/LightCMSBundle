LightCMS
========
LightCMS is a CMS (Content Manager System) which is built with Symfony 2 framework. 
Well, you can say "another CMS for what ?". Actually, many CMS make many thing, and need cpu ressource to display a website.

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

1. Download FulgurioLightCMSBundle and dependent bundles
2. Configure the Autoloader
3. Enable the Bundle
4. Import routing
5. Try it !

That's easy !

### Step 1: Download DoctrineFixturesBundle

First, you need to install [doctrine-fixture](http://symfony.com/doc/2.0/bundles/DoctrineFixturesBundle/index.html) for CMS data initialization.

**Using the vendors script**

Add the following lines in your `deps` file (you can do at the same time of DoctrineFixturesBundle):

``` ini
[FulgurioLightCMSBundle]
    git=http://github.com/fulgurio/LightCMSBundle.git
    target=/bundles/Fulgurio/LightCMSBundle
```

Just download the bundle with vendors loading tool :

``` bash
$ php bin/vendors install
```

### Step 2: Configure the Autoloader

Add the `Fulgurio` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Fulgurio\\LightCMSBundle' => __DIR__.'/../vendor/bundles',
));
```

### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Fulgurio\LightCMSBundle\FulgurioLightCMSBundle(),
    );
}
```

### Step 4: Import routing file

Now that you have activated and configured the bundle, all that is left to do is import the FulgurioLightCMSBundle routing file.

You need to put it on the bottom of your file, to be the last routes used (if no route parse, LightCMS try to found the page into the database)

``` yaml
FulgurioLightCMSBundle:
    resource: "@FulgurioLightCMSBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 5: Try it !

Just before to try, don't forget to clear your cache. And if everything's working well, you will see the homepage

License
-------
This bundle is under the MIT license. See the complete license in the bundle:

About
-----
LightCMSBundle is a [Fulgurio](https://github.com/fulgurio) initiative.

Reporting an issue or a feature request
---------------------------------------
Issues and feature requests are tracked in the [Github issue tracker](https://github.com/fulgurio/LightCMSBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
