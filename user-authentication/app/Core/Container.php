<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

/**
 * Tiny dependency-injection container with constructor autowiring.
 *
 * - bind():      new instance every time
 * - singleton(): one shared instance
 * - instance():  register an existing object
 * - get():       resolves registered services, or autowires concrete classes
 *
 * Bind an interface to an implementation to keep code depending on abstractions:
 *   $container->singleton(UserRepository::class, fn ($c) => new PdoUserRepository($c->get(Database::class)));
 */
final class Container
{
    /** @var array<string, array{factory: Closure, shared: bool}> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    public function bind(string $id, Closure $factory): void
    {
        $this->bindings[$id] = ['factory' => $factory, 'shared' => false];
    }

    public function singleton(string $id, Closure $factory): void
    {
        $this->bindings[$id] = ['factory' => $factory, 'shared' => true];
    }

    public function instance(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || isset($this->bindings[$id]);
    }

    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id])) {
            $binding = $this->bindings[$id];
            $object = ($binding['factory'])($this);
            if ($binding['shared']) {
                $this->instances[$id] = $object;
            }
            return $object;
        }

        return $this->autowire($id);
    }

    private function autowire(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Cannot resolve [{$class}]: not bound and class does not exist.");
        }

        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Cannot resolve [{$class}]: not instantiable. Bind it in bootstrap.php.");
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return new $class();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $arguments[] = $this->get($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                throw new RuntimeException(
                    "Cannot resolve parameter \${$parameter->getName()} of [{$class}]."
                );
            }
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
