<?php

namespace App\Security\Helper;

use App\Entity\Note;
use Symfony\Component\HttpFoundation\Request;

class NoteCodeValidator
{
    public const EDIT_CODE_KEY = 'edit_code';
    public const READ_CODE_KEY = 'read_code';
    
    public function isClearedToEdit(Request $request, Note $note)
    {
        $editCode = $request->query->get(self::EDIT_CODE_KEY);

        if (!$editCode) {
            $rawNote = $request->request->get('note');
            $editCode = $rawNote['editCode'] ?? null;
        }

        return $editCode && $editCode === $note->getEditCode();
    }

    public function isClearedToRead(Request $request, Note $note)
    {
        $readCode = $request->query->get(self::READ_CODE_KEY);

        return (string) $readCode === (string) $note->getReadCode();
    }
}
