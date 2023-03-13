<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $order = new Order();

            $numProducts = rand(1, 10);
            $order->setAmount($numProducts);
            for ($i = 0; $i < $numProducts; $i++) {
                $order->addProduct($this->getReference('product-' . rand(0, 9)));
            }

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class,
        ];
    }
}
