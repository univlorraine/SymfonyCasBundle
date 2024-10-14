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
                ->scalarNode('cas_service_base_url')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->example('https://yourapp.domain.com')
                    ->info('Url of the running app.')
                ->end()
                ->scalarNode('cas_ca_cert')
                    ->defaultNull()
                    ->example('/path/to/my/certificate.ca')
                    ->info('Path to the CAS server CA certificate.')
                ->end()
                ->scalarNode('cas_login_redirect')
                    ->defaultValue('/')
                    ->example('/restricted_zone')
                    ->info('Path to redirect after login success.')
                ->end()
                ->scalarNode('cas_logout_redirect')
                    ->defaultNull()
                    ->example('/public/access')
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
                ->scalarNode('public_access_regex')
                    ->defaultValue('')
                    ->example('#^/(public|other/(sub1|sub2))$#')
                    ->info('a regex that match publicly accessible URLs but give user if authenticated')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
