<?php namespace App\Providers;

use App\Services\EventStore;
use App\Services\ProjectorLoader;
use App\Services\ProjectorPositionRepository;
use Illuminate\Container\Container;
use Infrastructure\App\Services\FakeEventStore;
use Infrastructure\App\Services\LaravelProjectorLoader;
use Infrastructure\App\Services\LaravelProjectorPositionRepository;

class AppProviders
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function bind()
    {
        $this->container->singleton(
            Container::class,
                function() {
                    return $this->container;
                }
        );

        $this->container->bind(
            ProjectorPositionRepository::class,
                LaravelProjectorPositionRepository::class
        );

        $this->container->bind(
            ProjectorLoader::class,
                LaravelProjectorLoader::class
        );

        $this->container->bind(
            EventStore::class,
                FakeEventStore::class
        );
    }
}