<?php

namespace App\Tests\Api;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Tests\ApiBaseTestCase;

class OrdersTest extends ApiBaseTestCase
{
    public OrderRepository $orderRepository;
    public ProductRepository $productRepository;

    public function setup(): void
    {
        static::bootKernel();

        $container = static::getContainer();

        $this->orderRepository = $container->get(OrderRepository::class);
        $this->productRepository = $container->get(ProductRepository::class);
    }

    public function testGetCollectionReturnsValidCollection(): void
    {
        $response = static::createClient()->request('GET', '/orders');

        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $this->assertJsonContains(['@id' => '/orders']);

        $ordersCount = $this->orderRepository->count([]);

        $this->assertJsonContains(['hydra:totalItems' => $ordersCount], $response->getContent());
    }

    public function testGetItemReturnsValidItem(): void
    {
        $orderID = 1;

        $order = $this->orderRepository->findOneBy(['id' => $orderID]);

        $response = static::createClient()->request('GET', '/orders/' . $orderID);

        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $this->assertJsonContains(['@id' => '/orders/' . $orderID]);
        
        $this->assertJsonContains(['amount' => $order->getAmount()], $response->getContent());
    }

    public function testCanCreateItem(): void
    {
        $order = new Order();

        $order->setAmount(2);

        $product = $this->productRepository->find(1);

        $response = static::createClient()->request('POST', '/orders', [
            'json' => [
                'amount' => $order->getAmount(),
                'products' => [
                    'products/' . $product->getId()
                ]
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($response->getContent());

        $this->assertJsonContains(['amount' => $order->getAmount(), 'products' => [ '/products/' . $product->getId() ]], $response->getContent());
    }

    public function testCanDeleteItem(): void
    {
        $order = new Order();

        $order->setAmount(rand(1, 10));
        $this->orderRepository->save($order, true);

        $response = static::createClient()->request('DELETE', '/orders/' . $order->getId());

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull($this->orderRepository->find($order->getId()));
    }
}
