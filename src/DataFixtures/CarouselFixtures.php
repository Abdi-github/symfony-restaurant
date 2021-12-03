<?php

namespace App\DataFixtures;

use App\Entity\Carousel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarouselFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        foreach ($this->getCarouselData() as [$name, $description, $image, $status]) {
            $carousel = new Carousel();

            $carousel->setName($name);
            $carousel->setDescription($description);
            $carousel->setImage($image);
            $carousel->setStatus($status);

            $manager->persist($carousel);
        }

        $manager->flush();
    }

    private function getCarouselData()
    {
        return [
            ['Abdi Pizza', 'Italian Pizza With Cherry Tomatoes and Green Basil', 'pizza (4).png', \true],
            ['Abdi Burger', 'Best Burger of US', 'burger (3).png', false],
            ['Abdi Pasta', 'Best Italian Pasta', 'pasta (4).png', true]
        ];
    }
}
