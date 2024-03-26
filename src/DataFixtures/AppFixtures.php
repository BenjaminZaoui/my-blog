<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $this->loadUsers($manager, $faker);
        $this->loadArticles($manager, $faker);
    }

    private function createUser(
        ObjectManager $manager,
        string $email,
        string $password,
    ): void
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $password));
        $manager->persist($user);
    }

    private function loadUsers(ObjectManager $manager, Generator $faker): void
    {
        $this->createUser($manager, 'user@mail.dev', 'password', false,$faker);

        for ($i = 0; $i < 20; $i++) {
            $this->createUser($manager, $faker->email, "password", false,$faker);
        }

        $manager->flush();
    }

    public function loadArticles(ObjectManager $manager, Generator $faker): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 100; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(6));
            $article->setContent($faker->text(300));
            $article->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $article->setAuthor($faker->randomElement($users));
            $manager->persist($article);
        }

        $manager->flush();
    }



}
