<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('univ_lorraine_symfony_cas');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('cas_login_url')->end()
                ->scalarNode('cas_service_validate_url')->end()
                ->scalarNode('cas_logout_url')->end()
                ->scalarNode('cas_ticket_parameter')->defaultValue('ticket')->end()
                ->scalarNode('cas_service_parameter')->defaultValue('service')->end()
                ->scalarNode('xml_cas_namespace')->defaultValue('cas')->end()
                ->scalarNode('xml_username_attribute')->defaultValue('user')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
