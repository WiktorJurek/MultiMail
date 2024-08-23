<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory as FakerFactory;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private \Faker\Generator $faker;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = FakerFactory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $numUsers = 10;

        for ($i = 0; $i < $numUsers; $i++) {
            $name = $this->faker->firstName;
            $surname = $this->faker->lastName;
            $mail = strtolower($name . $surname . '@example.com');

            $user = new User();
            $user->setEmail($mail);
            $user->setName($name);
            $user->setSurname($surname);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
