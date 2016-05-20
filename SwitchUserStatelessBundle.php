<?php

namespace LaFourchette\SwitchUserStatelessBundle;

use LaFourchette\SwitchUserStatelessBundle\DependencyInjection\Security\SwitchUserStatelessFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SwitchUserStatelessBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SwitchUserStatelessFactory());
    }
}
