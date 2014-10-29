<?php

namespace Genj\SsoClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @package Genj\SsoClientBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('genj_sso_client');

        $rootNode
            ->children()
                ->scalarNode('broker_identifier')
                    ->info('The unique identifying name of this broker')
                    ->isRequired()
                ->end()
                ->scalarNode('broker_secret')
                    ->info('The secret belonging to this broker')
                    ->isRequired()
                ->end()
                ->scalarNode('server_url')
                    ->info('The SSO server url')
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}