<?php

namespace App\Controller;

use App\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @Route("/notes/{id}", name="note_show")
     */
    public function show(Note $note)
    {
        return $this->render('note/note_show.html.twig', [
            'note' => $note,
        ]);
    }
}
