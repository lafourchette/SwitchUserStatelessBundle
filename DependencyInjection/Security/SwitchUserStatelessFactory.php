<?php

namespace LaFourchette\SwitchUserStatelessBundle\DependencyInjection\Security;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SwitchUserStatelessFactory implements SecurityFactoryInterface
{
    const PROVIDER_SWITCH_USER_STATELESS = 'security.authentication.provider.switch_user_stateless';
    const LISTENER_SWITCH_USER_STATELESS = 'security.authentication.listener.switch_user_stateless';

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
        // The provider does nothing, but is required
        $providerId = sprintf('%s.%s', self::PROVIDER_SWITCH_USER_STATELESS, $id);
        $container->setDefinition(
            $providerId,
            new DefinitionDecorator(self::PROVIDER_SWITCH_USER_STATELESS)
        );

        // The listener does the logic
        $listenerId = sprintf('%s.%s', self::LISTENER_SWITCH_USER_STATELESS, $id);
        $container
            ->setDefinition(
                $listenerId,
                new DefinitionDecorator(self::LISTENER_SWITCH_USER_STATELESS)
            )
            ->replaceArgument(1, new Reference($userProvider))
            ->replaceArgument(3, $id)
            ->replaceArgument(6, $config['parameter'])
            ->replaceArgument(7, $config['role'])
        ;

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
        /* @var ArrayNodeDefinition $node */
        $node->children()
            ->scalarNode('parameter')->defaultValue('X-Switch-User')->end()
            ->scalarNode('role')->defaultValue('ROLE_ALLOWED_TO_SWITCH')->end()
        ;
    }
}
