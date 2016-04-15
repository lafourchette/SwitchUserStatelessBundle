<?php

namespace SwitchUserStatelessBundle\Controller;

use ApiPlatform\Core\JsonLd\Response as JsonLdResponse;
use Dunglas\ApiBundle\Api\ResourceCollectionInterface;
use Dunglas\ApiBundle\Api\ResourceInterface;
use Dunglas\ApiBundle\Exception\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/", service="switch_user_stateless.controller.api_platform_profile")
 */
class ApiPlatformProfileController extends ProfileController
{
    /**
     * @var ResourceCollectionInterface
     */
    private $resourceCollection;

    /**
     * @param NormalizerInterface              $serializer
     * @param TokenStorageInterface            $tokenStorage
     * @param AuthorizationCheckerInterface    $authorizationChecker
     * @param ResourceCollectionInterface|null $resourceCollection
     */
    public function __construct(
        NormalizerInterface $serializer,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        ResourceCollectionInterface $resourceCollection = null
    ) {
        parent::__construct($serializer, $tokenStorage, $authorizationChecker);

        $this->resourceCollection = $resourceCollection;
    }

    /**
     * @Route("/profile")
     * @Method({"GET", "HEAD"})
     *
     * @param Request $request
     *
     * @return Response|JsonLdResponse
     */
    public function profileAction(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        // API Platform 1
        if (null !== $this->resourceCollection) {
            return new JsonResponse(
                $this->serializer->normalize(
                    $user,
                    'json-ld',
                    $this->getResource($user)->getNormalizationContext() + [
                        'request_uri' => $request->getRequestUri(),
                    ]
                )
            );
        }

        // API Platform 2
        return new JsonLdResponse(
            $this->serializer->normalize(
                $user,
                'jsonld',
                [
                    'request_uri' => $request->getRequestUri(),
                    'resource_class' => get_class($this->tokenStorage->getToken()->getUser()),
                    'item_operation_name' => 'profile',
                ]
            )
        );
    }

    /**
     * @param mixed $user
     *
     * @throws InvalidArgumentException
     *
     * @return ResourceInterface
     */
    private function getResource($user)
    {
        if (!($resource = $this->resourceCollection->getResourceForEntity($user))) {
            throw new InvalidArgumentException(sprintf('The resource "%s" cannot be found.', get_class($user)));
        }

        return $resource;
    }
}
