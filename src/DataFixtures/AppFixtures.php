<?php

namespace App\DataFixtures;

use App\Entity\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $note = (new Note())
                ->setTitle("note_title_$i")
                ->setContent("note_content_$i")
                ->setUrl("note_url_$i")
            ;
            $manager->persist($note);
        }

        $manager->flush();
    }
}
