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
                )
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
                )
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
                )
            );
        }
        $container->setParameter('fulgurio_light_cms.models', $config['models']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}