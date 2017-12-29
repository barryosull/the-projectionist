<?php namespace Projectionist\Providers;

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
            \Projectionist\Services\ProjectorPositionLedger::class,
            \Projectionist\Infrastructure\Services\InMemory\ProjectorPositionLedger::class
        );

        $this->container->bind(
            \Projectionist\Services\ProjectorLoader::class,
            \Projectionist\Infrastructure\Services\Laravel\ProjectorLoader::class
        );

        $this->container->bind(
            \Projectionist\Services\EventStore::class,
            \Projectionist\Infrastructure\Services\InMemory\EventStore::class
        );

        if (getenv('APP_ENV') == 'testing') {
            $this->container->bind(
                \Projectionist\Services\ProjectorPlayer::class,
                \Projectionist\Services\EventClassProjectorPlayer::class
            );
        } else {
            $this->container->bind(
                \Projectionist\Services\ProjectorPlayer::class,
                \Projectionist\Services\EventSourcedProjectorPlayer::class
            );
        }

    }
}