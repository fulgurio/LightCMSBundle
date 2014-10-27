<?php
/*
 * This file is part of the LightCMSBundle package.
 *
 * (c) Fulgurio <http://fulgurio.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fulgurio\LightCMSBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FulgurioLightCMSExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('fulgurio_light_cms.allow_recursive_page_delete', $config['allow_recursive_page_delete']);
        $container->setParameter('fulgurio_light_cms.slug_suffix_separator', $config['slug_suffix_separator']);
        // Admin slug is not allowed
        if (!in_array('admin', $config['slug_exclusions']))
        {
           $config['slug_exclusions'][] = 'admin';
        }
        $container->setParameter('fulgurio_light_cms.slug_exclusions', $config['slug_exclusions']);
        if (count($config['langs']) > 1)
        {
            $langs = array();
            foreach ($config['langs'] as $lang) {
                $langs[$lang] = $lang;
            }
            $container->setParameter('fulgurio_light_cms.languages', $langs);
        }
        $container->setParameter('fulgurio_light_cms.wysiwyg', isset($config['wysiwyg']) ? $config['wysiwyg']: null);
        if (!isset($config['models']))
        {
            $config['models'] = array();
        }
        if (!isset($config['models']['standard']))
        {
            $config['models']['standard'] = array(
                'name' => 'standard',
                'front' => array(
                    'template' => 'FulgurioLightCMSBundle:models:standardFront.html.twig'
                ),
                'allow_childrens' => TRUE,
                'is_unique' => FALSE
            );
        }
        if (!isset($config['models']['redirect']))
        {
            $config['models']['redirect'] = array(
                'name' => 'redirect',
                'back' => array(
                    'form' =>     'Fulgurio\LightCMSBundle\Form\Type\AdminRedirectPageType',
                    'handler' =>  'Fulgurio\LightCMSBundle\Form\Handler\AdminRedirectPageHandler',
                    'template' => 'FulgurioLightCMSBundle:models:redirectAdminAddForm.html.twig',
                    'view' =>     'FulgurioLightCMSBundle:models:redirectAdminView.html.twig',
                ),
                'front' => array(
                    'controller' => 'Fulgurio\LightCMSBundle\Controller\FrontRedirectPageController::redirect',
                ),
                'allow_childrens' => TRUE,
                'is_unique' => FALSE
            );
        }
        if ($container->hasParameter('fulgurio_light_cms.models'))
        {
            $models = $container->getParameter('fulgurio_light_cms.models');
            $config['models'] = array_merge($config['models'], $models);
        }
           $container->setParameter('fulgurio_light_cms.models', $config['models']);
        if (!isset($config['thumbs']))
        {
            $config['thumbs'] = array();
        }
            if (!isset($config['thumbs']['small']))
        {
            $config['thumbs']['small'] = array('width' => 100, 'height' => 100);
        }
        if (!isset($config['thumbs']['medium']))
        {
            $config['thumbs']['medium'] = array('width' => 200, 'height' => 200);
        }
        $container->setParameter('fulgurio_light_cms.thumbs', $config['thumbs']);

        if (isset($config['menus']))
        {
            $menus = array();
            foreach ($config['menus'] as $menu)
            {
                $menus[$menu] = $menu;
            }
            $container->setParameter('fulgurio_light_cms.menus', $menus);
        }

        if (isset($config['roles']) && !empty($config['roles']))
        {
            $container->setParameter('fulgurio_light_cms.users.roles', $config['roles']);
        }
        else
        {
            $container->setParameter('fulgurio_light_cms.users.roles', array('ROLE_ADMIN'));
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}