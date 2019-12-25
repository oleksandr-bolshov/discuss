<?php

declare(strict_types=1);

namespace Apathy\Discuss\Tests;

use Apathy\Discuss\DiscussServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withFactories('./database/factories');
        $this->loadMigrationsFrom('./database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            DiscussServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
