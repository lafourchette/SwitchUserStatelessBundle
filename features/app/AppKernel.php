<?php

use LaFourchette\SwitchUserStatelessBundle\SwitchUserStatelessBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Test purpose micro-kernel.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new SwitchUserStatelessBundle(),
        ];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->add('/profile', 'kernel:profileAction', 'profile');
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 'SwitchUserStatelessBundle',
            'test' => null,
            'serializer' => ['enabled' => true],
        ]);

        $c->loadFromExtension('security', [
            'encoders' => [UserInterface::class => 'plaintext'],
            'providers' => [
                'in_memory' => [
                    'memory' => [
                        'users' => [
                            'admin' => ['password' => 'admin', 'roles' => ['ROLE_ALLOWED_TO_SWITCH']],
                            'john.doe' => ['password' => 'john.doe', 'roles' => ['ROLE_USER']],
                        ],
                    ],
                ],
            ],
            'firewalls' => [
                'main' => [
                    'pattern' => '^/',
                    'stateless' => true,
                    'switch_user_stateless' => true,
                    'http_basic' => null,
                ],
            ],
            'access_control' => [
                ['path' => '^/', 'roles' => 'IS_AUTHENTICATED_FULLY'],
            ],
        ]);
    }

    public function profileAction()
    {
        return new JsonResponse($this->container->get('serializer')->normalize(
            $this->container->get('security.token_storage')->getToken()->getUser(), 'json')
        );
    }
}
