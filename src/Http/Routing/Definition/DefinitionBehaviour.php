<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing\Definition;

use PhoneBurner\SaltLite\Enum\Enum;
use PhoneBurner\SaltLite\Http\Domain\HttpMethod;
use PhoneBurner\SaltLite\Http\Routing\Route;
use PhoneBurner\SaltLite\Type\Type;
use Psr\Http\Server\RequestHandlerInterface;

trait DefinitionBehaviour
{
    private const string REGEX = '#^(?:\\\[a-zA-Z]|[a-zA-Z])(?:[\-_.\\\]?[a-zA-Z0-9]+)*$#';

    private array $attributes;

    private array $methods;

    private string $path;

    private function setAttributes(array $attributes): void
    {
        $route = (string)($attributes[Route::class] ?? '');
        if ($route !== '' && ! \preg_match(self::REGEX, $route)) {
            throw new \InvalidArgumentException('invalid name: ' . $route);
        }

        $request_handler = (string)($attributes[RequestHandlerInterface::class] ?? '');
        if ($request_handler !== '' && ! Type::isClassStringOf(RequestHandlerInterface::class, $request_handler)) {
            throw new \InvalidArgumentException('handler must be type of: ' . RequestHandlerInterface::class);
        }

        $this->attributes = $attributes;
    }

    private function setMethods(HttpMethod|string ...$methods): void
    {
        $this->methods = \array_values(
            \array_unique(
                Enum::values(
                    ...\array_map(HttpMethod::instance(...), $methods),
                ),
            ),
        );
    }

    public function with(callable ...$fns): self
    {
        return \array_reduce(
            $fns,
            static fn(self $definition, callable $fn): self => $fn($definition),
            $this,
        );
    }
}
