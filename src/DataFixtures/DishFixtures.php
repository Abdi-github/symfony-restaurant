<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Dish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DishFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        foreach ($this->getDishData() as [$category_id, $name, $description, $price, $discounted_price, $image, $status]) {
            $dish = new Dish();

            $category = $manager->getRepository(Category::class)->find($category_id);

            $dish->setCategory($category);
            $dish->setName($name);
            $dish->setDescription($description);
            $dish->setPrice($price);
            $dish->setDiscountedPrice($discounted_price);
            $dish->setImage($image);
            $dish->setStatus($status);

            $manager->persist($dish);
        }

        $manager->flush();
    }

    private function getDishData()
    {
        return [
            [1, 'Diablo', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 11.99, null, 'pizza (1).png', \true],
            [1, 'Carbonara', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 12.99, 9.99, 'pizza (2).png', true],
            [1, 'Capricciosa', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 9.99, null, 'pizza (3).png', true],
            [1, 'Prosciutto', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 13.99, 10.99, 'pizza (4).png', true],

            [2, 'Diablo', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 11.99, null, 'burger (1).png', \true],
            [2, 'Carbonara', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 12.99, 9.99, 'burger (2).png', true],
            [2, 'Capricciosa', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 9.99, null, 'burger (3).png', true],
            [2, 'Prosciutto', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 13.99, 10.99, 'burger (4).png', true],

            [3, 'Diablo', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 11.99, null, 'pasta (1).png', \true],
            [3, 'Carbonara', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 12.99, 9.99, 'pasta (2).png', true],
            [3, 'Capricciosa', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 9.99, null, 'pasta (3).png', true],
            [3, 'Prosciutto', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 13.99, 10.99, 'pasta (4).png', true],

            [4, 'Diablo', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 11.99, null, 'salad (1).png', \true],
            [4, 'Carbonara', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 12.99, 9.99, 'salad (2).png', true],
            [4, 'Capricciosa', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 9.99, null, 'salad (3).png', true],
            [4, 'Prosciutto', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 13.99, 10.99, 'salad (4).png', true],

            [5, 'Diablo', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 11.99, null, 'desert1.png', \true],
            [5, 'Carbonara', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 12.99, 9.99, 'desert2.png', true],
            [5, 'Capricciosa', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 9.99, null, 'desert3.png', true],
            [5, 'Prosciutto', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 13.99, 10.99, 'desert4.png', true],

            [6, 'Diablo', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 11.99, null, 'drink (1).png', \true],
            [6, 'Carbonara', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 12.99, 9.99, 'drink (2).png', true],
            [6, 'Capricciosa', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 9.99, null, 'drink (3).png', true],
            [6, 'Prosciutto', 'Classic marinara sauce, authentic old-world pepperoni, all-natural Italy', 13.99, 10.99, 'drink (4).png', true],


        ];
    }
}
