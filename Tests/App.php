<?php namespace ProjectionistTests\Bootstrap;

use Illuminate\Container\Container;

class App
{
    private static $container;

    public static function container(): Container
    {
        if (!self::$container) {
            self::$container = new Container();
        }
        return self::$container;
    }

    public static function make(string $class)
    {
        return self::container()->make($class);
    }
}