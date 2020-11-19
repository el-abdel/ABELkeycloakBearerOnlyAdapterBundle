<?php

namespace ABEL\Bundle\keycloakBearerOnlyAdapterBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ABELkeycloakBearerOnlyAdapterExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('abel_keycloak_bearer_only_adapter.keycloak_bearer_user_provider');
        $definition->replaceArgument(0, $config['issuer']);
        $definition->replaceArgument(1, $config['realm']);
        $definition->replaceArgument(2, $config['client_id']);
        $definition->replaceArgument(3, $config['client_secret']);
    }

    public function getAlias()
    {
        return 'abel_keycloak_bearer_only_adapter';
    }
}
