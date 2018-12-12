<?php
/**
 * Created by PhpStorm.
 * User: ilya
 * Date: 03.12.18
 * Time: 20:32
 */
namespace AppBundle\DataFixtures;

use AppBundle\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMovieData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $movie = new Movie();
        $movie->setTitle('Green Mile');
        $movie->setYear(1999);
        $movie->setTime(189);
        $movie->setDescription('Just a movie description.');

        $manager->persist($movie);
        $manager->flush();
    }
}