<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public const PRODUCT_REFERENCE = "product-";

    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($generator->words(2, true));
            $product->setPrice($generator->numberBetween(200, 20000) / 100);

            $this->addReference(self::PRODUCT_REFERENCE . $i, $product);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
