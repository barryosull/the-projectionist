<?php

require __DIR__."/../vendor/autoload.php";

$provider = new \ProjectionistTests\TestProvider( \ProjectionistTests\Bootstrap\App::container() );
$provider->boot();