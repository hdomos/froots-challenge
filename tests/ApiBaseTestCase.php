<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

abstract class ApiBaseTestCase extends ApiTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
    }

}
