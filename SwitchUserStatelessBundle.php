<?php

namespace LaFourchette\SwitchUserStatelessBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use LaFourchette\SwitchUserStatelessBundle\DependencyInjection\Security\SwitchUserStatelessFactory;

class SwitchUserStatelessBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var \Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SwitchUserStatelessFactory());
    }
}
