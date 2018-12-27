<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 27.12.18
 * Time: 22:44
 */

namespace AppBundle\DataFixtures;


use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('john_doe');
        $user1->setApiKey('90d8dfgdsd9sf90sd790sdf90s90df');

        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('jane_doe');
        $user2->setApiKey('99e8dfgdsd9sf90sd790sdfe0s90df');

        $manager->persist($user2);

        $manager->flush();
    }
}