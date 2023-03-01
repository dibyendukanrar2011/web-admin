<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setFirstName('dev');
        $user->setLastName('admin');
        $user->setCellphone('12345');
        $user->setUsername('devadmin');
        $user->setEmail('dibyendukanrar2011@gmail.com');
        $user->setPassword('$2y$13$252Obdb0sGD6zxfIdKTC.uyq/dqBKo7joIDs2h1nK7hNbhKDK1qSy');
        $user->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($user);
        $manager->flush();
    }
}
