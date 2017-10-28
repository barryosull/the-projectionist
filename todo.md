# Projector Restructure
In the current DQL framework we have some messy code around projectors and workflows.
This repo is an attempt to explore potential new structures to make using and extending these concepts easier.

Todo:
- Write acceptance tests for the new components
    - Use an in memory repo
- Write unit tests for appropriate units
- Write integration tests for infrastructure    

Ideas:
- Extract how events are played into projectors as a strategy
- Have one that just plays from where each projector left off
- Have a smarter one that play events one by one to projectors if they're at the same position
