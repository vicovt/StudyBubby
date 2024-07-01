<?php

namespace App\Controller;

use App\Entity\Note;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @Route("/notes", name="note_index", methods={"GET"})
     */
    public function index(): Response
    {
        $notes = $this-> getDoctrine()->getRepository(Note::class)->findAll();

        return $this->json($notes);
    }

    /**
     * @Route("/notes/{id}", name="note_show", methods={"GET"})
     */
    public function show(Note $note): Response
    {
        return $this->json($note);
    }

    /**
     * @Route("/notes", name="note_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $note = new Note();
        $note->setTitle($data['title'] ?? '');
        $note->setContent($data['content'] ?? '');
        $note->setCreatedAt(new \DateTime());

        $entityManager->persist($note);
        $entityManager->flush();

        return $this->json($note, Response::HTTP_CREATED);
    }

    /**
     * @Route("/notes/{id}", name="note_update", methods={"PUT"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager, Note $note): Response
    {
        $data = json_decode($request->getContent(), true);

        $note->setTitle($data['title'] ?? $note->getTitle());
        $note->setContent($data['content'] ?? $note->getContent());

        $entityManager->flush();

        return $this->json($note);
    }

    /**
     * @Route("/notes/{id}", name="note_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager, Note $note): Response
    {
        $entityManager->remove($note);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
