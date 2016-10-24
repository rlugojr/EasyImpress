<?php

/**
 * This file is part of the EasyImpress package.
 *
 * (c) Alexandre Rock Ancelet <alex@orbitale.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orbitale\Bundle\EasyImpressBundle\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PresentationConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('presentation');

        $rootNode
            ->children()
                ->scalarNode('name')->isRequired()->end()

                ->variableNode('slides')->end()

                ->floatNode('inactive_opacity')->defaultValue(1)->end()

                ->arrayNode('data')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('transition_duration')->defaultValue(1000)->end()
                        ->integerNode('min_scale')->defaultValue(1)->end()
                    ->end()
                ->end()

                ->arrayNode('attr')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('style')->defaultValue('')->end()
                        ->scalarNode('class')
                            ->defaultValue('impress_slides_container')
                            ->validate()
                            ->always(function($v){
                                if (false === strpos($v, 'impress_slides_container')) {
                                    $v = trim('impress_slides_container '.$v);
                                }

                                return $v;
                            })
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('increments')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('x')->defaultValue(null)->end()
                        ->integerNode('y')->defaultValue(null)->end()
                        ->integerNode('z')->defaultValue(null)->end()
                        ->integerNode('rotate')->defaultValue(null)->end()
                        ->integerNode('rotate_x')->defaultValue(null)->end()
                        ->integerNode('rotate_y')->defaultValue(null)->end()
                        ->integerNode('rotate_z')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
