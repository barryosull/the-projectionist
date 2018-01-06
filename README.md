# The Projectionist

If you are building an EventSourced/CQRS system in PHP, you need a solid system to handle building your projections. Enter the "projectionist".

This is a library that makes it easy to consume events and manage the lifecycle of projectors in PHP. It's based on a lot of trial and error from building these kinds of systems, so my hope is that it will allow others to leapfrog us and gain from our mistakes.

![Projectionist in action](https://res.cloudinary.com/practicaldev/image/fetch/s--0Wje2n09--/c_limit%2Cf_auto%2Cfl_progressive%2Cq_auto%2Cw_880/https://thepracticaldev.s3.amazonaws.com/i/ea3uvjpnhca5wokt6tnx.png)

## How it works
The projectonist is given a collection of projectors. It can either boot or play these projectors.

Booting projectors prepares new projectors for launch. If they're `run_from_launch`, they will just get set to the latest event, and no events will be played. Other projectors will be played up to the most recent event. This should be run as part of your release script, ensuring that all new projectors and played and up to date before you make the app live.

Playing projectors takes all the active projectors that aren't `run_once`, and plays them to the latest event. This is intended to run in the background when the system is live, constantly listening for new events and attempting to apply them.

## How to use this library
To use this library, you must write integrations for your system. We don't know what how you've implemented your system, so instead we've made it easy to integrate with this system, no matter what your implementation.

There are three adapters and one stategy that must be implemented:
- Adapters:
  - EventStore - Check if there are events, get the latest event, or get a stream of events after an event ID.
  - EventStream - Get events one at a time, until there are none left
  - EventWrapper - Wraps your event, you just need to implement how you get the id, so the projectionist can keep track of the projectors position.
- Strategy:
  - EventHandler - Play an event into a projector. Simple to write, gives you full flexibility.

The adapters act as integration points to your application, allowing your system and the projectionist to work together, no matter what the implementation.

The strategy is EventHandler, it allows you define how your projectors work. By default it uses a class name based handler. Here's what it looks like.



However, you can write your own version however you want. This means you're not stuck with the handler system we've implemented.

## Using a different storage engine for projector poisitons
By default this system uses redis to keep track of projector poisitons. If you'd like to use another implementation, you'll need to write your own. This isn't too hard though, just create an adapter that implements the interface and passes the integration tests. 
(Probably needs more detail, but easiest way to figure out how to do this is look at the Redis implementation and the integration test for it)
It is based on an article I am about to release, I'll link it here once I have.

This system is implementation agnostic, it focuses purely on the projectionist concept and makes no assumptions on how your implementation technologies. That said, it does feature some example code for a laravel implementation.

## Modes
Projectors tend to have three distinct modes.
1. `run_from_start`: Start at the first event and play as normal
3. `run_from_launch`: Start at most recent event, only play forward from there
2. `run_once`: Start at the first event, only run once

These modes allow for various usecases. Most projectors will be `run_from_start`, while `run_from_launch` is useful when you only want the projector triggered by new events, not existing ones. `run_once` is useful for the opposite reason, only run through existing events, ignore new ones.

These can be configured by add a `MODE` const to your projector, and setting the appropriate mode.
```php
class Projector extends BaseTestProjector
{
    const MODE = ProjectorMode::RUN_FROM_LAUNCH;
}
```

## TODO
- Add REDIS adapters
    - Get tests work
- Restructure test folders to make more sense
- Write tutorial for the adapters
- Have a smarter projectionist that groups projectors by position, then plays events one by one to projectors if they're at the same position. One stream, multiple projectors.