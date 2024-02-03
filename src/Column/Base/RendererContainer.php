<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Psr\Container\ContainerInterface;
use Yiisoft\Injector\Injector;
use Yiisoft\Yii\DataView\Column\ColumnRendererInterface;

/**
 * @internal
 */
final class RendererContainer
{
    private Injector $injector;

    /**
     * @psalm-var array<string, ColumnRendererInterface>
     */
    private array $cache = [];

    /**
     * @psalm-var array<string, array>
     */
    private array $constructorArguments = [];


    public function __construct(ContainerInterface $dependencyContainer)
    {
        $this->injector = new Injector($dependencyContainer);
    }

    /**
     * @psalm-param class-string<ColumnRendererInterface> $class
     */
    public function get(string $class): ColumnRendererInterface
    {
        if (!isset($this->cache[$class])) {
            $this->cache[$class] = $this->injector->make($class, $this->constructorArguments[$class] ?? []);
        }

        return $this->cache[$class];
    }

    /**
     * @psalm-param array<string, array> $constructorArguments
     */
    public function addConstructorArguments(array $constructorArguments): self
    {
        $new = clone $this;
        foreach ($constructorArguments as $class => $arguments) {
            $new->constructorArguments[$class] = $arguments;
            unset($new->cache[$class]);
        }
        return $new;
    }
}
