<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ServiceFactory;

use PhoneBurner\SaltLite\Container\ServiceFactory;
use Psr\Container\ContainerInterface;

final readonly class StaticMethodServiceFactory implements ServiceFactory
{
    /**
     * @param class-string $class
     */
    public function __construct(
        private string $class,
        private string $method = 'make',
    ) {
    }

    public function __invoke(ContainerInterface $container, string $id): object
    {
        return $this->class::{$this->method}($container);
    }
}
