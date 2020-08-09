<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Security\Helper\NoteCodeValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @Route("/notes/{url}", name="note_show")
     */
    public function show(
        Note $note,
        Request $request,
        NoteCodeValidator $noteCodeValidator
    ) {
        if (!$noteCodeValidator->isClearedToRead($request, $note)) {
            $this->addFlash("warning", "This note requires a valid code.");
            return $this->render('note/note_ask_read_code.html.twig', ['note' => $note]);
        }
        return $this->render('note/note_show.html.twig', [
            'note' => $note,
        ]);
    }

    /**
     * @Route(
     *     "/notes/edit/{url}",
     *     name="note_edit"
     * )
     */
    public function edit(
        Note $note,
        Request $request,
        EntityManagerInterface $manager,
        NoteCodeValidator $noteCodeValidator
    ) {
        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);

        if ($noteForm->isSubmitted() && $noteForm->isValid()) {

            if (!$noteCodeValidator->isClearedToEdit($request, $note)) {
                $this->addFlash("error", "Wrong edit code.");

                return $this->redirectToRoute('note_edit', ['url' => $note->getUrl()]);
            }

            $manager->flush();
            $this->addFlash("success", "A note has been updated.");

            return $this->redirectToRoute("home");
        }

        return $this->render("note/note_edit.html.twig", [
            'note' => $note,
            'noteForm' => $noteForm->createView()
        ]);
    }

    /**
     * @Route(
     *     "/notes/delete/{url}",
     *     name="note_delete"
     * )
     */
    public function delete(
        Request $request,
        Note $note,
        EntityManagerInterface $manager,
        NoteCodeValidator $noteCodeValidator
    ) {
        if (!$noteCodeValidator->isClearedToEdit($request, $note)) {
            $this->addFlash("error", "Wrong edit code.");

            return $this->redirectToRoute('home');
        }

        $manager->remove($note);
        $manager->flush();
        $this->addFlash("success", "A note has been deleted.");

        return $this->redirectToRoute("home");
    }
}
