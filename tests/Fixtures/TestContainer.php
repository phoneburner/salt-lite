<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use Psr\Container\ContainerInterface;

/**
 * Simple container for testing
 */
class TestContainer implements ContainerInterface
{
    private array $services = [];
    private array $requested_services = [];

    public function get(string $id)
    {
        $this->requested_services[$id] = true;

        if (! isset($this->services[$id])) {
            throw new \RuntimeException("Service not found: $id");
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function registerService(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }

    public function wasServiceRequested(string $id): bool
    {
        return isset($this->requested_services[$id]);
    }
}
