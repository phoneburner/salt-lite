<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ServiceFactory;

use PhoneBurner\SaltLite\Container\ServiceFactory;
use Psr\Container\ContainerInterface;

final readonly class MethodServiceFactory implements ServiceFactory
{
    /**
     * @param class-string|object $class_or_object
     */
    public function __construct(
        private object|string $class_or_object,
        private string $method = 'make',
    ) {
    }

    public function __invoke(ContainerInterface $app, string $id): object
    {
        $object = \is_string($this->class_or_object) ? $app->get($this->class_or_object) : $this->class_or_object;
        if (! \method_exists($object, $this->method)) {
            throw new \LogicException(\sprintf('Method "%s" does not exist', $this->method));
        }

        return $object->{$this->method}($app);
    }
}
