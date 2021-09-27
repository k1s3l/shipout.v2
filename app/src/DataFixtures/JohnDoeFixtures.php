<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class JohnDoeFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setLastName('Doe')
            ->setFirstName('John')
            ->setMiddleName('Undefined')
            ->setEmail('john.doe@example.com')
            ->setIsVerified(true)
            ->setUsername('john_doe')
            ->setPassword($this->passwordHasher->hashPassword($user, 'J0HN_D03'))
            ->setDateOfBirth((new \DateTimeImmutable())->modify('-16 years'))
        ;

        $manager->persist($user);
        $manager->flush();
    }
}
