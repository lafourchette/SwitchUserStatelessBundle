<?php

namespace LaFourchette\SwitchUserStatelessBundle\Security\Firewall;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * SwitchUserStatelessListener allows a user to impersonate another one temporarily.
 * Unlike @see \Symfony\Component\Security\Http\Firewall\SwitchUserListener it is stateless
 * (no 302 redirection) and default parameter is X-Switch-User in headers.
 */
class SwitchUserStatelessListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserProviderInterface
     */
    private $provider;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var string
     */
    private $providerKey;

    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    /**
     * @var string
     */
    private $usernameParameter;

    /**
     * @var string
     */
    private $role;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param TokenStorageInterface          $tokenStorage
     * @param UserProviderInterface          $provider
     * @param UserCheckerInterface           $userChecker
     * @param string                         $providerKey
     * @param AccessDecisionManagerInterface $accessDecisionManager
     * @param LoggerInterface                $logger
     * @param string                         $usernameParameter
     * @param string                         $role
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserProviderInterface $provider,
        UserCheckerInterface $userChecker,
        $providerKey,
        AccessDecisionManagerInterface $accessDecisionManager,
        LoggerInterface $logger = null,
        $usernameParameter = 'X-Switch-User',
        $role = 'ROLE_ALLOWED_TO_SWITCH',
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->provider = $provider;
        $this->userChecker = $userChecker;
        $this->providerKey = $providerKey;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->usernameParameter = $usernameParameter;
        $this->role = $role;
        $this->logger = $logger ?: new NullLogger();
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param GetResponseEvent $event
     * 
     * @throws AuthenticationException
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Check if specified parameter is sent in headers
        if (!$request->headers->get($this->usernameParameter)) {
            return;
        }

        $this->tokenStorage->setToken($this->attemptSwitchUser($request));
    }

    /**
     * @param Request $request A Request instance
     *
     * @return TokenInterface|null The new TokenInterface if successfully switched, null otherwise
     *
     * @throws AccessDeniedException
     * @throws TokenNotFoundException
     */
    private function attemptSwitchUser(Request $request)
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            throw new TokenNotFoundException();
        }

        if (false === $this->accessDecisionManager->decide($token, [$this->role])) {
            throw new AccessDeniedException();
        }

        $username = $request->headers->get($this->usernameParameter);

        $this->logger->info(
            'Attempt to switch user',
            ['previousUsername' => $token->getUsername(), 'newUsername' => $username]
        );

        $user = $this->provider->loadUserByUsername($username);
        $this->userChecker->checkPostAuth($user);

        $roles = $user->getRoles();
        $roles[] = new SwitchUserRole('ROLE_PREVIOUS_ADMIN', $token);

        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->providerKey, $roles);

        if (null !== $this->dispatcher) {
            /** @var UserInterface $user */
            $user = $token->getUser();

            $switchEvent = new SwitchUserEvent($request, $user);
            $this->dispatcher->dispatch(SecurityEvents::SWITCH_USER, $switchEvent);
        }

        return $token;
    }
}
