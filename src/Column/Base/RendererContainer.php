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
    private array $configs = [];

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
            $this->cache[$class] = $this->injector->make($class, $this->configs[$class] ?? []);
        }

        return $this->cache[$class];
    }

    /**
     * @psalm-param array<string, array> $configs
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
