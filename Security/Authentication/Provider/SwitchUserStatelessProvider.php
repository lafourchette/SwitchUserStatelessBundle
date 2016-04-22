<?php

namespace LaFourchette\SwitchUserStatelessBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * This class does nothing, but is required.
 *
 * @see \SwitchUserStatelessBundle\DependencyInjection\Security\SwitchUserStatelessFactory
 */
class SwitchUserStatelessProvider implements AuthenticationProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        throw new AuthenticationException('SwitchUserStatelessProvider MUST NOT be used to authenticate');
    }
}
