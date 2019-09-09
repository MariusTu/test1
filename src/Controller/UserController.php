<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;
use function Sodium\add;

/**
 * User controller.
 * @Route("/api", name="api_")
 */
class UserController extends FOSRestController
{
    /**
     * Lists all Users.
     * @Rest\Get("/users")
     *
     * @return Response
     */
    public function getUserAction()
    {
        // deny access from inside controller or you can use injection pattern and inject Security object
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $repository = $this->getDoctrine()->getRepository(User::class);
        $entities = $repository->findall();
        return $this->handleView($this->view($entities));
    }

    /**
     * As an admin I can add users. A user has a name.
     * @Rest\Post("/user")
     *
     * @param Request $request
     * @return Response
     */
    public function postUserAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($data['name']);
        $user->setUsername($data['username']);
        $user->setRoles($data['roles']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        //return new Response(null, Response::HTTP_CREATED);
        return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
    }

    /**
     * As an admin I can delete users.
     * @Rest\Delete("/user")
     *
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($data['id']);

        if ($user) {
            $em->remove($user);
            $em->flush();
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // might return some othe indication when not found
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * As an admin I can assign users to a group they aren’t already part of.
     * @Rest\Post("/usergroup")
     *
     * @param Request $request
     * @return Response
     */
    // PATCH didn't work so for now using POST
    public function postUsergroupAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($data['id']);

        if (!$user) {
            // no such user
            return $this->handleView($this->view(['status' => 'ok1'], Response::HTTP_CREATED));
        }

        if(in_array($data['group'], $user->getGroups())) {
            // already part of group
            return $this->handleView($this->view(['status' => 'ok2'], Response::HTTP_CREATED));
        }

        $groupsArray[] = $user->getGroups();
        array_merge($groupsArray, array($data['group']));
        $user->setGroups($groupsArray);
        $em->persist($user);
        $em->flush();

        return $this->handleView($this->view(['status' =>  'ok3'], Response::HTTP_CREATED));
    }
}