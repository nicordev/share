<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(NoteRepository $noteRepository, Request $request, EntityManagerInterface $manager)
    {
        $notes = $noteRepository->findAll();

        $note = new Note();
        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);

        if ($noteForm->isSubmitted() && $noteForm->isValid()) {
            if (empty($note->getUrl())) {
                $note->setUrl(explode(' ', $note->getTitle())[0]);
            }

            $manager->persist($note);
            $manager->flush();
            $this->addFlash("success", "A note has been created.");

            return $this->redirectToRoute("home");
        }

        return $this->render('home/index.html.twig', [
            'notes' => $notes,
            'noteForm' => $noteForm->createView()
        ]);
    }
}
