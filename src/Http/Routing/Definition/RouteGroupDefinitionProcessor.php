<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing\Definition;

use PhoneBurner\SaltLite\Http\Routing\Definition\RouteGroupDefinition;

interface RouteGroupDefinitionProcessor
{
    public function __invoke(RouteGroupDefinition $definition): RouteGroupDefinition;
}
