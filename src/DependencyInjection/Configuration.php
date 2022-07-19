<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const CAS_VERSION_3_0 = '3.0';
    const CAS_VERSION_2_0 = '2.0';
    const CAS_VERSION_1_0 = '1.0';
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('univ_lorraine_symfony_cas');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('cas_url')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->example('casserver.herokuapp.com')
                    ->info('Main url of the CAS Server.')
                ->end()
                ->scalarNode('cas_context')
                    ->example('/cas')
                    ->info('Request path of the CAS Server.')
                ->end()
                ->scalarNode('cas_port')
                    ->cannotBeEmpty()
                    ->defaultValue(443)
                    ->example('443')
                    ->info('Port of the CAS Server')
                ->end()
                ->scalarNode('cas_logout_path')
                    ->defaultValue('/logout')
                    ->example('/logout')
                    ->info('CAS Server path for logging out.')
                ->end()
                ->scalarNode('cas_logout_redirect')
                    ->example('home')
                    ->info('Route or URL to redirect after successful logout.')
                ->end()
                ->enumNode('cas_version')
                    ->values([
                        self::CAS_VERSION_3_0,
                        self::CAS_VERSION_2_0,
                        self::CAS_VERSION_1_0,
                    ])
                    ->defaultValue(self::CAS_VERSION_2_0)
                    ->example('2.0')
                    ->info('Version of the CAS Server.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
