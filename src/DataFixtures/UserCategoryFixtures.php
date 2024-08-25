<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\UserCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $categoryRepository = $manager->getRepository(Category::class);

        $users = $userRepository->findAll();
        $categories = $categoryRepository->findAll();

        if (empty($users) || empty($categories)) {
            throw new \Exception('No users or categories found in the database');
        }

        foreach ($users as $user) {
            $selectedCategories = array_rand($categories, rand(1, count($categories)));

            if (!is_array($selectedCategories)) {
                $selectedCategories = [$selectedCategories];
            }

            foreach ($selectedCategories as $index) {
                $category = $categories[$index];

                $userCategory = new UserCategory();
                $userCategory->setUser($user);
                $userCategory->setCategory($category);

                $manager->persist($userCategory);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
