<?php

namespace App\Tests\Api;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Tests\ApiBaseTestCase;

class ProductOrdersTest extends ApiBaseTestCase
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

    public function testProductOrdersContainSpecificOrder(): void
    {
        $product = new Product();
        $product->setPrice('2.1');
        $product->setName('Test');
        $this->productRepository->save($product, true);

        $order = new Order();
        $order->setAmount(1);
        $order->addProduct($product);

        $this->orderRepository->save($order, true);

        $response = static::createClient()->request('GET', '/products/' . $product->getId() . '/orders');

        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $decodedOrders = json_decode($response->getContent(), true)['hydra:member'];

        $this->assertIsArray($decodedOrders);
        $this->assertCount(1, $decodedOrders);
        $this->assertTrue($decodedOrders[0]['id'] === $order->getId());
    }
}
