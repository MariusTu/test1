<?php

namespace App\Controller;
use App\Entity\UserGroup;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Group controller.
 * @Route("/api", name="api_")
 */
class UserGroupController extends FOSRestController
{
//    /**
//     * @Route("/group", name="group")
//     */
//    public function index()
//    {
//        return $this->render('group/index.html.twig', [
//            'controller_name' => 'UserGroupController',
//        ]);
//    }

    /**
     * Create Group.
     * @Rest\Post("/group")
     *
     * @param Request $request
     * @return Response
     */
    public function postUserAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $group = new UserGroup();
        $group->setName($data['name']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();

        return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
    }
}
