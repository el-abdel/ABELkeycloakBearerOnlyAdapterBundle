<?php


namespace ABEL\Bundle\keycloakBearerOnlyAdapterBundle;


use ABEL\Bundle\keycloakBearerOnlyAdapterBundle\DependencyInjection\ABELkeycloakBearerOnlyAdapterExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ABELkeycloakBearerOnlyAdapterBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ABELkeycloakBearerOnlyAdapterExtension();
        }
        return $this->extension;
    }
}
