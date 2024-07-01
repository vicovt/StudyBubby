<?php

namespace App\Controller;

use App\Entity\Summary;
use App\Form\SummaryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class SummaryController extends AbstractController
{
    /**
     * @Route("/summaries", name="summary_index", methods={"GET"})
     */
    public function index(): Response
    {
        $summaries = $this->getDoctrine()->getRepository(Summary::class)->findAll();

        return $this->json($summaries);
    }

    /**
     * @Route("/summaries/{id}", name="summary_show", methods={"GET"})
     */
    public function show(Summary $summary): Response
    {
        return $this->json($summary);
    }

    /**
     * @Route("/summaries", name="summary_create", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $summary = new Summary();
        $summary->setContent($data['content'] ?? '');

        // Assume you have the Note ID in the request data
        $noteId = $data['note_id'] ?? null;
        if ($noteId) {
            $note = $this->getDoctrine()->getRepository(Note::class)->find($noteId);
            if ($note) {
                $summary->setNote($note);
            } else {
                throw $this->createNotFoundException('Note not found for ID '.$noteId);
            }
        } else {
            throw new \InvalidArgumentException('Note ID must be provided.');
        }

        $entityManager->persist($summary);
        $entityManager->flush();

        return $this->json($summary, Response::HTTP_CREATED);
    }

    /**
     * @Route("/summaries/{id}", name="summary_update", methods={"PUT"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager, Summary $summary): Response
    {
        $data = json_decode($request->getContent(), true);

        $summary->setContent($data['content'] ?? $summary->getContent());

        $entityManager->flush();

        return $this->json($summary);
    }

    /**
     * @Route("/summaries/{id}", name="summary_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager, Summary $summary): Response
    {
        $entityManager->remove($summary);
        $entityManager->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
