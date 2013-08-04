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
                 ->scalarNode('allow_posts_list')->defaultValue(TRUE)->end()
            ->end()
            ->children()
                ->scalarNode('slug_suffix_separator')->defaultValue('AAA')->end()
            ->end()
            ->children()
                ->arrayNode('langs')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('tiny_mce')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('content_css')
                            ->defaultValue('bundles/fulguriolightcms/css/bootstrap.min.css')
                        ->end()
                        ->scalarNode('theme')
                            ->defaultValue('advanced')
                        ->end()
                        ->scalarNode('skin')
                            ->defaultValue('default')
                        ->end()
                        ->scalarNode('plugins')
                            ->defaultValue('autolink,lists,spellchecker,style,layer,table,advhr,advimage,advlink,emotions,iespell,twitterbootstrappopup,media,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template')
                        ->end()
                        ->scalarNode('theme_advanced_buttons1')
                            ->defaultValue('bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect')
                        ->end()
                        ->scalarNode('theme_advanced_buttons2')
                            ->defaultValue('paste,pastetext,pasteword,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor')
                        ->end()
                        ->scalarNode('theme_advanced_buttons3')
                            ->defaultValue('tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions')
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('posts')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('format')
                            ->defaultValue('Y/m/d')
                        ->end()
                        ->scalarNode('nb_per_page')
                            ->defaultValue(10)
                        ->end()
                    ->end()
                ->end()
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
                    ->addDefaultsIfNotSet()
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
                            ->scalarNode('allow_childrens')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('menus')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
                    return $treeBuilder;
    }
}
