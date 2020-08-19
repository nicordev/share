<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    /**
     * @Route("/notes/{url}", name="note_show")
     */
    public function show(
        Note $note
    ) {
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
        EntityManagerInterface $manager
    ) {
        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);

        if ($noteForm->isSubmitted() && $noteForm->isValid()) {
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
        Note $note,
        EntityManagerInterface $manager,
        Request $request,
        string $deleteCode
    ) {
        if ($request->query->get('deleteCode') !== $deleteCode) {
            throw new NotFoundHttpException();
        }

        $manager->remove($note);
        $manager->flush();
        $this->addFlash("success", "A note has been deleted.");

        return $this->redirectToRoute("home");
    }
}
