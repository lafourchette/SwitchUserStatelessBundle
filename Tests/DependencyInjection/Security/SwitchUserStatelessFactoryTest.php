<?php

namespace LaFourchette\SwitchUserStatelessBundle\Tests\DependencyInjection\Security;

use LaFourchette\SwitchUserStatelessBundle\DependencyInjection\Security\SwitchUserStatelessFactory;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SwitchUserStatelessFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $builder = new ContainerBuilder();

        $factory = new SwitchUserStatelessFactory();
        $config = ['parameter' => 'myParameter', 'role' => 'myRole'];

        list($providerId, $listenerId, $defaultEntryPoint) = $factory
            ->create($builder, 'myId', $config, 'myUserProvider', 'myEntryPoint');

        $this->assertEquals('security.authentication.provider.switch_user_stateless.myId', $providerId);
        $this->assertEquals('security.authentication.listener.switch_user_stateless.myId', $listenerId);
        $this->assertEquals($defaultEntryPoint, 'myEntryPoint');
    }

    public function testGetPosition()
    {
        $factory = new SwitchUserStatelessFactory();
        $this->assertEquals('http', $factory->getPosition());
    }

    public function testGetKey()
    {
        $factory = new SwitchUserStatelessFactory();
        $this->assertEquals('switch_user_stateless', $factory->getKey());
    }

    public function testAddConfiguration()
    {
        $scalarNodeMock = $this->prophesize(ScalarNodeDefinition::class);
        $nodeBuilderMock = $this->prophesize(NodeBuilder::class);
        $nodeMock = $this->prophesize(ArrayNodeDefinition::class);

        $nodeMock->children()->willReturn($nodeBuilderMock->reveal())->shouldBeCalledTimes(1);

        $nodeBuilderMock->scalarNode(Argument::type('string'))->willReturn($scalarNodeMock->reveal())->shouldBeCalledTimes(2);

        $scalarNodeMock->defaultValue('X-Switch-User')->willReturn($nodeMock->reveal())->shouldBeCalledTimes(1);
        $scalarNodeMock->defaultValue('ROLE_ALLOWED_TO_SWITCH')->willReturn($nodeMock->reveal())->shouldBeCalledTimes(1);

        $nodeMock->end()->willReturn($nodeBuilderMock->reveal())->shouldBeCalledTimes(2);

        $factory = new SwitchUserStatelessFactory();
        $factory->addConfiguration($nodeMock->reveal());
    }
}
