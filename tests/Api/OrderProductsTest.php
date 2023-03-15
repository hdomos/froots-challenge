<?php

namespace App\Tests\Api;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Tests\ApiBaseTestCase;

class OrderProductsTest extends ApiBaseTestCase
{
    public ProductRepository $productRepository;
    public OrderRepository $orderRepository;

    public function setup(): void
    {
        static::bootKernel();

        $container = static::getContainer();

        $this->productRepository = $container->get(ProductRepository::class);
        $this->orderRepository = $container->get(OrderRepository::class);
    }

    public function testOrderProductsContainSpecificProduct(): void
    {
        $product = new Product();
        $product->setPrice('2.1');
        $product->setName('Test');
        $this->productRepository->save($product, true);

        $order = new Order();
        $order->setAmount(1);
        $order->addProduct($product);

        $this->orderRepository->save($order, true);

        $response = static::createClient()->request('GET', '/orders/' . $order->getId() . '/products');

        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $decodedProducts = json_decode($response->getContent(), true)['hydra:member'];

        $this->assertIsArray($decodedProducts);
        $this->assertCount(1, $decodedProducts);
        $this->assertTrue($decodedProducts[0]['id'] === $product->getId());
    }
}
