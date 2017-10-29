<?php namespace Bootstrap;

use App\Providers\AppProvider;
use Illuminate\Container\Container;

class App
{
    private static $container;

    public function __construct()
    {
        if (!self::$container) {
            self::$container = new Container();
        }
    }

    public function bootstrap()
    {
        $this->loadProviders();
    }

    private function loadProviders()
    {
        $provider = new AppProvider(self::$container);
        $provider->boot();
    }

    public static function make(string $class)
    {
        return self::$container->make($class);
    }
}