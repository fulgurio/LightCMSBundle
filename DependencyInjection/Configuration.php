<?php

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
                            ->defaultValue('autolink,lists,spellchecker,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,media,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template')
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
        ;
        return $treeBuilder;
    }
}
