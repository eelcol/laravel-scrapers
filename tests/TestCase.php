<?php

namespace Eelcol\LaravelScrapers\Tests;


use Eelcol\LaravelScrapers\LaravelScrapersServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelScrapersServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}