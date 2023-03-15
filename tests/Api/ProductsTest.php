<?php

namespace App\Tests\Api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Tests\ApiBaseTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ProductsTest extends ApiBaseTestCase
{
    public ProductRepository $repository;

    public function setup(): void
    {
        static::bootKernel();

        $container = static::getContainer();

        $this->repository = $container->get(ProductRepository::class);
    }

    public function testGetCollectionReturnsValidCollection(): void
    {
        $response = static::createClient()->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $this->assertJsonContains(['@id' => '/products']);

        $productsCount = $this->repository->count([]);

        $this->assertJsonContains(['hydra:totalItems' => $productsCount], $response->getContent());
    }

    public function testGetItemReturnsValidItem(): void
    {
        $productID = 1;

        $product = $this->repository->findOneBy(['id' => $productID]);

        $response = static::createClient()->request('GET', '/products/' . $productID);

        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $this->assertJsonContains(['@id' => '/products/' . $productID]);
        
        $this->assertJsonContains(['name' => $product->getName(), 'price' => $product->getPrice()], $response->getContent());
    }

    public function testCanCreateItem(): void
    {
        $product = new Product();

        $product->setPrice(rand(10, 2000) / 100);
        $product->setName('TEST PRODUCT');

        $response = static::createClient()->request('POST', '/products', [
            'json' => [
                'price' => $product->getPrice(),
                'name' => $product->getName(),
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($response->getContent());

        $this->assertJsonContains(['name' => $product->getName(), 'price' => $product->getPrice()], $response->getContent());
    }

    public function testCantCreateItemWithoutPrice(): void
    {
        $response = static::createClient()->request('POST', '/products', [
            'json' => [
                'name' => 'BLANK PRICE PRODUCT',
            ]
        ]);

        $this->assertResponseIsUnprocessable('price: This value should not be blank.');
    }

    /** @return array<array{0: string, 1: string}> */
    public static function invalidPriceProvider(): array
    {
        return [
            ['NaN', 'price: This value should be of type numeric.'],
            ['-0.5', 'price: This value should be positive.'],
            ['Definitely not a valid price', 'price: This value should be of type numeric.'],
            ['1.123', 'price: This value should be a multiple of 0.01.'],
        ];
    }

    #[DataProvider('invalidPriceProvider')]
    public function testCantCreateItemWithInvalidPrice(string $invalidPrice, string $message): void
    {
        $response = static::createClient()->request('POST', '/products', [
            'json' => [
                'name' => 'PRODUCT WITH INVALID PRICE',
                'price' => $invalidPrice,
            ]
        ]);

        $this->assertResponseIsUnprocessable($message);
    }

    public function testCantCreateItemWithBadInput(): void
    {
        $response = static::createClient()->request('POST', '/products', [
            'json' => [
                'name' => 'PRODUCT WITH INVALID PRICE',
                'price' => 11,
            ]
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    

    public function testCanDeleteItem(): void
    {
        $product = new Product();

        $product->setPrice(rand(10, 2000) / 100);
        $product->setName('TEST PRODUCT');
        $this->repository->save($product, true);

        $response = static::createClient()->request('DELETE', '/products/' . $product->getId());

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull($this->repository->find($product->getId()));
    }
}
