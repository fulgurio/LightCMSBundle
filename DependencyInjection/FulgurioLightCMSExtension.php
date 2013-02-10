<?php

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
        if (count($config['langs']) > 1)
        {
            $container->setParameter('fulgurio_light_cms.languages', $config['langs']);
        }
        $container->setParameter('fulgurio_light_cms.tiny_mce', $config['tiny_mce']);
        $container->setParameter('fulgurio_light_cms.posts', $config['posts']);

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
            );
        }
        if (!isset($config['models']['postsList']))
        {
            $config['models']['postsList'] = array(
                'name' => 'posts_list',
                'back' => array(
                    'form' =>     'Fulgurio\LightCMSBundle\Form\AdminPostsListPageType',
                    'handler' =>  'Fulgurio\LightCMSBundle\Form\AdminPostsListPageHandler',
                    'template' => 'FulgurioLightCMSBundle:models:postsListAdminAddForm.html.twig',
                    'view' =>     'FulgurioLightCMSBundle:models:postsListAdminView.html.twig',
                ),
                'front' => array(
                    'template' =>   'FulgurioLightCMSBundle:models:standardFront.html.twig',
                    'controller' => 'Fulgurio\LightCMSBundle\Controller\FrontPostPageController::list',
                ),
                'allow_childrens' => FALSE,
            );
        }
        if (!isset($config['models']['redirect']))
        {
            $config['models']['redirect'] = array(
                'name' => 'redirect',
                'back' => array(
                    'form' =>     'Fulgurio\LightCMSBundle\Form\AdminRedirectPageType',
                    'handler' =>  'Fulgurio\LightCMSBundle\Form\AdminRedirectPageHandler',
                    'template' => 'FulgurioLightCMSBundle:models:redirectAdminAddForm.html.twig',
                    'view' =>     'FulgurioLightCMSBundle:models:redirectAdminView.html.twig',
                ),
                'front' => array(
                    'controller' => 'Fulgurio\LightCMSBundle\Controller\FrontRedirectPageController::redirect',
                ),
                'allow_childrens' => FALSE,
            );
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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}