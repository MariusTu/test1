<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;

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
     * Create User.
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
     * Delete User.
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
}