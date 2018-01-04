<?php namespace ProjectionistTests;

use Illuminate\Container\Container;
use Projectionist\AdapterFactory;

class TestProvider
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function boot()
    {
        $this->bind();
    }

    public function bind()
    {
        $this->container->singleton(
            Container::class,
                function() {
                    return $this->container;
                }
        );

        $this->container->singleton(AdapterFactory::class, AdapterFactory\InMemory::class);
    }
}