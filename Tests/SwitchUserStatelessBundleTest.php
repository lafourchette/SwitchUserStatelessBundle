<?php

namespace SwitchUserStatelessBundle\Tests;

use Prophecy\Prophecy\ObjectProphecy;
use SwitchUserStatelessBundle\DependencyInjection\Security\SwitchUserStatelessFactory;
use SwitchUserStatelessBundle\SwitchUserStatelessBundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SwitchUserStatelessBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new SwitchUserStatelessBundle();

        $extension = $this->getSecurityExtensionMock();
        $extension->addSecurityListenerFactory(new SwitchUserStatelessFactory())->shouldBeCalled();

        $container = $this->getContainerBuilderMock();
        $container->getExtension('security')->willReturn($extension)->shouldBeCalled();

        $bundle->build($container->reveal());
    }

    /**
     * @return ObjectProphecy
     */
    private function getContainerBuilderMock()
    {
        return $this->prophesize(ContainerBuilder::class);
    }

    /**
     * @return ObjectProphecy
     */
    private function getSecurityExtensionMock()
    {
        return $this->prophesize(SecurityExtension::class);
    }
}
