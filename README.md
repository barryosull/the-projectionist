# The Projectionist

If you are building an EventSourced/CQRS system in PHP, you need a solid system to handle building your projections. Enter the "projectionist".

This is a library that makes it easy to consume events and manage the lifecycle of projectors in PHP. It's based on a lot of trial and error from building these kinds of systems, so my hope is that it will allow others to leapfrog us and gain from our mistakes.

![Projectionist in action](https://res.cloudinary.com/practicaldev/image/fetch/s--0Wje2n09--/c_limit%2Cf_auto%2Cfl_progressive%2Cq_auto%2Cw_880/https://thepracticaldev.s3.amazonaws.com/i/ea3uvjpnhca5wokt6tnx.png)

## How it works
The projectonist is given a collection of projectors. It can either boot or play these projectors.

Booting projectors prepares new projectors for launch. If they're `run_from_launch`, they will just get set to the latest event, and no events will be played. Other projectors will be played up to the most recent event. This should be run as part of your release script, ensuring that all new projectors and played and up to date before you make the app live.

Playing projectors takes all the active projectors that aren't `run_once`, and plays them to the latest event. This is intended to run in the background when the system is live, constantly listening for new events and attempting to apply them.

## How to use this library
This how you create a projectionist.

```php
// Define the config for the projectionist system
$config = new Config\InMemory(); 

// Create a factory
$projectionist_factory = new ProjectionistFactory($config); 

// Load all your projectors into an array
$projectors = [new RunFromLaunch, new RunFromStart];

// Create the projectionist 
$projectionist = $projectionist_factory->make($projectors); 

// Boot the projectors
$projectionist->boot();

// Play the projectors
$projectionist->play();
```

That's it. The tricky part is in the details though. To use this library, you must write integrations for your system. We don't know what how you've implemented your system, so instead we've made it easy to integrate with this system, no matter what your implementation.

To do this, you have to implement the config for the projectonist.

Config is an interface that outputs three adapters and one strategy, these also need to be implemented.

- Adapters:
  - EventStore - Check if there are events, get the latest event, or get a stream of events after an event ID.
  - EventStream - Get events one at a time, until there are none left
  - EventWrapper - Wraps your event, you just need to implement how you get the id, so the projectionist can keep track of the projectors position.

The adapters act as integration points to your application, allowing your system and the projectionist to work together, no matter what the implementation.

You can also choose to override the default event handler by defining your own EventHandler Strategy.
- Strategy:
  - EventHandler - Play an event into a projector. Simple to write, gives you full flexibility.

It allows you define how your projectors work. By default it uses a class name based handler, which handle projectors with this handler style.

```php
<?php 

use Domain\Selling\Events\CartCheckedOut;

class Projector 
{
    public function whenCartCheckedOut(CartCheckedOut $event)
    {
        // Call method on projecion of servie, whatever you need to do
    }    
}
```

The above is handled by this strategy.

```php
<?php namespace Projectionist\Strategy\EventHandler;

use Projectionist\Strategy\EventHandler;

class ClassName implements EventHandler
{
    public function handle($event, $projector)
    {
        $method = $this->handlerFunctionName($this->className($event));

        if (method_exists($projector, $method)) {
            $projector->$method($event);
        }
    }

    private function className($event)
    {
        $namespaces = explode('\\', get_class($event));
        return last($namespaces);
    }

    private function handlerFunctionName(string $type): string
    {
        return "when".$type;
    }
}
```

However, you can write your own version however you want. This means you're not stuck with the handler system we've implemented.

## Using a different storage engine for projector positions
By default this system uses redis to keep track of projector positions. If you'd like to use another implementation, you'll need to write your own. This isn't too hard though, just create an adapter that implements the interface and passes the integration tests. 
You probably needs more detail, but the easiest way to figure out how to do this is look at the Redis implementation and the integration test for it.

## Modes
Projectors tend to have three distinct modes, which control how each behaves when booted, or played.
1. `run_from_start`: Start at the first event and play as normal
3. `run_from_launch`: Start at most recent event, only play forward from there
2. `run_once`: Start at the first event, only run once

These modes allow for various usecases. Most projectors will be `run_from_start`, which is the default (ie. you don't need to define a mode), while `run_from_launch` is useful when you only want the projector triggered by new events, not existing ones. `run_once` is useful for the opposite reason, only run through existing events, ignore new ones.

These can be configured by add a `MODE` const to your projector, and setting the appropriate mode.
```php
<?php

class Projector
{
    const MODE = ProjectorMode::RUN_FROM_LAUNCH;
}
```

## Versioning and the power of seamless deploys
Projectors can be versioned. This means that while it is the same projector, it has changed in some way that requires all the events to be played though again.

Versioning is an important part of any projector system, and so we've made it as easy as possible to handle.

When booting, if the projector version has not been booted or played before, it will be considered new. This is important, as during a `boot` process, you'll want the new version to be booted up, while leaving the old version running on the live codebase.
This allows you to boot a new version of a projector, without causing issue with the existing live version. If there's an issue during the boot, the process will fail, and the existing projectors will keep playing. If everything goes well, the existing codebase is told to stop playing projectors, and the new codebase takes over, allowing a seamless transition.

Here show you set the version of a projector.
```php
<?php

class Projector
{
    const VERSION = 2;
}
```

Be default each projector is assumed to be version 1. When you need to bump the version, simple define the const and bump the number, the projectionist will take care of the rest.

## TODOs
My list of todos for this project

- Get Redis ProjectorPositionLedger tests to pass
- Restructure test folders to make more sense
- Write a better tutorial for the adapters
- Have a smarter projectionist that groups projectors by position, then plays events one by one to projectors if they're at the same position. One stream, multiple projectors.