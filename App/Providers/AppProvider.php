<?php namespace App\Providers;

use Illuminate\Container\Container;

class AppProvider
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

        $this->container->singleton(
            \App\Services\ProjectorPositionRepository::class,
                \Infrastructure\App\Services\InMemory\ProjectorPositionRepository::class
        );

        $this->container->bind(
            \App\Services\ProjectorLoader::class,
                \Infrastructure\App\Services\Laravel\ProjectorLoader::class
        );

        $this->container->bind(
            \App\Services\EventStore::class,
                \Infrastructure\App\Services\InMemory\EventStore::class
        );
    }
}