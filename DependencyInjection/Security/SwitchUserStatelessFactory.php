<?php

namespace LaFourchette\SwitchUserStatelessBundle\DependencyInjection\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SwitchUserStatelessFactory implements SecurityFactoryInterface
{
    /**
     * @param ContainerBuilder      $container
     * @param string                $id
     * @param array                 $config
     * @param UserProviderInterface $userProvider
     * @param string                $defaultEntryPoint
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        //the provider does nothing, but is required
        $providerId = 'security.authentication.provider.switch_user_stateless.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('security.authentication.provider.switch_user_stateless'));

        //the listener does the logic
        $listenerId = 'security.authentication.listener.switch_user_stateless.'.$id;
        $listener = $container
            ->setDefinition($listenerId, new DefinitionDecorator('security.authentication.listener.switch_user_stateless'));
        $listener->replaceArgument(1, new Reference($userProvider));
        $listener->replaceArgument(3, $id);
        $listener->replaceArgument(6, $config['parameter']);
        $listener->replaceArgument(7, $config['role']);

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return 'http';
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'switch_user_stateless';
    }

    /**
     * @param NodeDefinition $node
     */
    public function addConfiguration(NodeDefinition $node)
    {
        /* @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node */
        $node->children()
            ->scalarNode('parameter')->defaultValue('X-Switch-User')->end()
            ->scalarNode('role')->defaultValue('ROLE_ALLOWED_TO_SWITCH')->end();
    }
}
