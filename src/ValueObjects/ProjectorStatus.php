<?php namespace Projectionist\ValueObjects;

class ProjectorStatus
{
    const NEW = "new";
    const WORKING = "working";
    const BROKEN = "broken";
    const STALLED = "stalled";
}