<?php

namespace SwitchUserStatelessBundle\Tests\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfileController extends Controller
{
    /**
     * @Route("/profile")
     * @Method({"GET", "HEAD"})
     */
    public function profileAction()
    {
        return new JsonResponse($this->get('serializer')->normalize($this->get('security.token_storage')->getToken()->getUser(), 'json'));
    }
}
