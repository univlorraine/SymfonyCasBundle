<?php

namespace UnivLorraine\Bundle\SymfonyCasBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class UnivLorraineSymfonyCasExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $authenticator = $container->autowire('univ_lorraine_symfony_cas.authenticator',
            'UnivLorraine\Bundle\SymfonyCasBundle\Security\CasAuthenticator');
        $authenticator->setArguments(array($config));

        $container->register('univ_lorraine_symfony_cas.user_provider',
         'UnivLorraine\Bundle\SymfonyCasBundle\Security\User\CasUserProvider');
    }
}
