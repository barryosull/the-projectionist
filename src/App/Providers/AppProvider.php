<?php namespace Projectionist\App\Providers;

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
            \Projectionist\App\Services\ProjectorPositionLedger::class,
            \Projectionist\Infrastructure\App\Services\InMemory\ProjectorPositionLedger::class
        );

        $this->container->bind(
            \Projectionist\App\Services\ProjectorLoader::class,
            \Projectionist\Infrastructure\App\Services\Laravel\ProjectorLoader::class
        );

        $this->container->bind(
            \Projectionist\App\Services\EventStore::class,
            \Projectionist\Infrastructure\App\Services\InMemory\EventStore::class
        );

        if (getenv('APP_ENV') == 'testing') {
            $this->container->bind(
                \Projectionist\App\Services\ProjectorPlayer::class,
                \Projectionist\App\Services\EventClassProjectorPlayer::class
            );
        } else {
            $this->container->bind(
                \Projectionist\App\Services\ProjectorPlayer::class,
                \Projectionist\App\Services\EventSourcedProjectorPlayer::class
            );
        }

    }
}