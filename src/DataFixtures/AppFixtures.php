<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $message = new Message();
            $message->setEmail(sprintf('some_email%d@symfony-api.demo', $i));
            $message->setMessage(sprintf('Message sent from: %d', $i));
            $manager->persist($message);
        }

        $manager->flush();
    }
}
