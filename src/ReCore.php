<?php

namespace Nukeflame\Core;

class ReCore
{
    public function greet(string $name): string
    {
        // logger()->debug('core package initialized');

        return "Hello, {$name}!";
    }
}
