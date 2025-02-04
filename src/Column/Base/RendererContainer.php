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
     * @psalm-var array<class-string, ColumnRendererInterface>
     */
    private array $cache = [];

    /**
     * @psalm-var array<class-string, array>
     */
    private array $configs = [];

    public function __construct(ContainerInterface $dependencyContainer)
    {
        $this->injector = new Injector($dependencyContainer);
    }

    /**
     * Get an instance of colum renderer implementation configured with {@see addConfigs()}.
     *
     * @psalm-param class-string<ColumnRendererInterface> $class
     */
    public function get(string $class): ColumnRendererInterface
    {
        if (!isset($this->cache[$class])) {
            $this->cache[$class] = $this->injector->make($class, $this->configs[$class] ?? []);
        }

        return $this->cache[$class];
    }

    /**
     * Add configurations for a column renderers.
     *
     * @psalm-param array<class-string, array> $configs An array of configurations for {@see get()}. Keys are column
     * renderer class names. Values are arrays of constructor arguments either indexed by argument name or having integer
     * index if applied sequentially.
     */
    public function addConfigs(array $configs): self
    {
        $new = clone $this;
        foreach ($configs as $class => $config) {
            $new->configs[$class] = isset($new->configs[$class])
                ? array_merge($new->configs[$class], $config)
                : $config;
            unset($new->cache[$class]);
        }
        return $new;
    }
}
