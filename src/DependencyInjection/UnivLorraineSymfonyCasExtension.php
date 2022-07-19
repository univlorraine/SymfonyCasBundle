<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class UnivLorraineSymfonyCasExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->autowire('univ_lorraine_symfony_cas.authenticator',
            'UnivLorraine\Bundle\SymfonyCasBundle\Security\CasAuthenticator');

        $container->register('univ_lorraine_symfony_cas.user_provider',
         'UnivLorraine\Bundle\SymfonyCasBundle\Security\User\CasUserProvider');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $definition = $container->getDefinition('univ_lorraine_symfony_cas.authentication_service');
        $definition->setArgument('$config', $config);
    }
}
