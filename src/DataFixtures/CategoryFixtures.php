<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        foreach ($this->getCategoryData() as $name) {
            $category = new Category();

            $category->setName($name);

            $manager->persist($category);
        }

        $manager->flush();
    }

    private function getCategoryData()
    {
        return [
            'Pizza',
            'Burger',
            'Pasta',
            'Salad',
            'Desert',
            'Drink',
        ];
    }
}
