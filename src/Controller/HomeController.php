<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(NoteRepository $noteRepository)
    {
        $notes = $noteRepository->findAll();

        return $this->render('home/index.html.twig', [
            'notes' => $notes,
        ]);
    }
}
