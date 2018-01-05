# The Projectionist

If you are building an EventSourced/CQRS system in PHP, you need a solid system to handle building your projections. Enter the "projectionist".

At it's simplest, this is a library that makes it easy to consume events and build projections in PHP, both at release and run-time. It's based on a lot of trial and error from building these kinds of systems, so my hope is that it will allow others to leapfrog me and gain from my mistakes.

![Projectionist in action](https://res.cloudinary.com/practicaldev/image/fetch/s--0Wje2n09--/c_limit%2Cf_auto%2Cfl_progressive%2Cq_auto%2Cw_880/https://thepracticaldev.s3.amazonaws.com/i/ea3uvjpnhca5wokt6tnx.png)

It is based on an article I am about to release, I'll link it here once I have.

This system is implementation agnostic, it focuses purely on the projectionist concept and makes no assumptions on how your implementation technologies. That said, it does feature some example code for a laravel implementation.

## Usecases
The system comes with two 

- Build new Projections at launch
- Trigger apply new events to existing projectors

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