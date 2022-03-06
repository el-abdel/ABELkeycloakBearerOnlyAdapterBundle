<?php


namespace ABEL\Bundle\keycloakBearerOnlyAdapterBundle;


use ABEL\Bundle\keycloakBearerOnlyAdapterBundle\DependencyInjection\ABELkeycloakBearerOnlyAdapterExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ABELkeycloakBearerOnlyAdapterBundle extends Bundle
{
    /**
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new ABELkeycloakBearerOnlyAdapterExtension();
        }
        return $this->extension;
    }
}
