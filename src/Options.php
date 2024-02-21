<?php

declare (strict_types=1);

namespace Golden;

use Closure;

function extension(string $extension): Closure
{
    return fn(Config $config) => $config->setExtension($extension);
}

function folder(string $prefix): Closure
{
    return fn(Config $config) => $config->setPrefix($prefix);
}

function waitApproval(): Closure
{
    return fn(Config $config) => $config->waitApproval();
}


function snapshot(string $snapshot): Closure
{
    return fn(Config $config) => $config->setSnapshot($snapshot);
}
