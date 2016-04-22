<?php

namespace LaFourchette\SwitchUserStatelessBundle\Tests\Security\Authentication\Provider;

use LaFourchette\SwitchUserStatelessBundle\Security\Authentication\Provider\SwitchUserStatelessProvider;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SwitchUserStatelessProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $token = $this->getTokenMock();
        $provider = new SwitchUserStatelessProvider();
        $this->assertFalse($provider->supports($token->reveal()));
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function testAuthenticate()
    {
        $token = $this->getTokenMock();
        $provider = new SwitchUserStatelessProvider();
        $provider->authenticate($token->reveal());
    }

    /**
     * @return ObjectProphecy|TokenInterface
     */
    private function getTokenMock()
    {
        return $this->prophesize(TokenInterface::class);
    }
}
