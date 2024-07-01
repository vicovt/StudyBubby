<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface; // Asegúrate de que esta línea esté presente

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->json($users);
    }

    /**
     * @Route("/users/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->json($user);
    }

    /**
     * @Route("/users", name="user_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setUsername($data['username'] ?? '');
        $user->setPassword($passwordEncoder->encodePassword($user, $data['password'] ?? ''));
        $user->setRoles($data['roles'] ?? []);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }

    /**
     * @Route("/users/{id}", name="user_update", methods={"PUT"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, User $user): Response
    {
        $data = json_decode($request->getContent(), true);

        $user->setUsername($data['username'] ?? $user->getUsername());
        if (isset($data['password'])) {
            $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
        }
        $user->setRoles($data['roles'] ?? $user->getRoles());

        $entityManager->flush();

        return $this->json($user);
    }

    /**
     * @Route("/users/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager, User $user): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
