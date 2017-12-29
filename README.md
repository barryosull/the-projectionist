# The Projectionist

If you are building an EventSourced/CQRS system in PHP, you need a solid system to handle building your projections. Enter the "projectionist".

At it's simplest, this is a library that makes it easy to consume events and build projections in PHP, both at release and run-time. It's based on a lot of trial and error from building these kinds of systems, so my hope is that it will allow others to leapfrog me and gain from my mistakes.

![Projectionist in action](https://res.cloudinary.com/practicaldev/image/fetch/s--0Wje2n09--/c_limit%2Cf_auto%2Cfl_progressive%2Cq_auto%2Cw_880/https://thepracticaldev.s3.amazonaws.com/i/ea3uvjpnhca5wokt6tnx.png)

It is based on an article I am about to release, I'll link it here once I have.

This system is implementation agnostic, it focusses purely on the projectionist concept and makes no assumptions on how you implement the underlying technologies. That said, it does feature some example code for a laravel implementation.

## Usecases
- Build new Projections at launch
- Trigger apply new events to existing projectors

## TODO
At the moment this library is mostly a proof of concept. As time goes on I plan to extend it to allow easy integration, which includes writing a readme.

Top todos
- Put lib in its own namespace
- Move around folder structure, add src, etc...