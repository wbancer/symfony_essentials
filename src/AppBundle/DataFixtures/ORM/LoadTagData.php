<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Tag;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tags = ['company', 'home', 'important'];

        foreach ($tags as $tag) {
            $obj = new Tag();
            $obj->setName($tag);
            $manager->persist($obj);
        }

        $manager->flush();
    }
}
