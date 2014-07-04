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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fulgurio_light_cms');
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('allow_recursive_page_delete')->defaultValue(FALSE)->end()
            ->end()
            ->children()
                ->scalarNode('slug_suffix_separator')->defaultValue('-')->end()
            ->end()
            ->children()
                ->arrayNode('slug_exclusions')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('langs')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->children()
                ->scalarNode('wysiwyg')->end()
            ->end()
            ->children()
                ->arrayNode('thumbs')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('width')->end()
                            ->scalarNode('height')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('models')
                    ->useAttributeAsKey('name')
//                    ->addDefaultsIfNotSet()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('file')->end()
                            ->arrayNode('back')
                                ->children()
                                    ->scalarNode('form')->end()
                                    ->scalarNode('handler')->end()
                                    ->scalarNode('template')->end()
                                    ->scalarNode('view')->end()
                                ->end()
                            ->end()
                            ->arrayNode('front')
                                ->children()
                                    ->scalarNode('controller')->end()
                                    ->scalarNode('template')->end()
                                ->end()
                            ->end()
                            ->scalarNode('allow_childrens')
                                ->defaultValue(TRUE)
                            ->end()
                            ->scalarNode('is_unique')
                                ->defaultValue(FALSE)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('menus')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('roles')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
