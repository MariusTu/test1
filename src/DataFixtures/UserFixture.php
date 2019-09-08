<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $admin = new User();
        $admin->setName("admin");
        $admin->setUsername("administrator");
        $adminRoles[] = array("ROLE_ADMIN");
        $admin->setRoles($adminRoles);
        $manager->persist($admin);

        $user = new User();
        $user->setName("user");
        $user->setUsername("username");
        $userRoles[] = array("ROLE_USER");
        $user->setRoles($userRoles);
        $manager->persist($user);

        $manager->flush();
    }
}
