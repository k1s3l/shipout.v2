<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private $passwordHashers;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user
                ->setLastName($faker->lastName)
                ->setFirstName($faker->firstName)
                ->setMiddleName($faker->firstNameMale)
                ->setEmail($faker->email)
                ->setIsVerified($i % 2 == 0)
                ->setUsername($faker->userName)
                ->setPassword($this->passwordHasher->hashPassword($user, $faker->password))
                ->setDateOfBirth($faker->dateTime('2005-01-01', 'UTC'))
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
