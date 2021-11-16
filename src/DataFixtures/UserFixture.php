<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setUsername('usuario')
            ->setPassword('$2y$13$dGWI1tp7FjVJ999skse51ua/uxcUzbZC1ITnz0WBjs7m5jXFcwsRa');
        
        $manager->persist($user);

        $manager->flush();
    }
}
