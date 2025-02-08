<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Psr\Container\ContainerInterface;
use Yiisoft\Injector\Injector;
use Yiisoft\Yii\DataView\Column\ColumnRendererInterface;

/**
 * `RendererContainer` manages the creation and configuration of column renderers.
 *
 * @internal This class is not part of the public API and may change without notice.
 */
final class RendererContainer
{
    private Injector $injector;

    /**
     * @var array Cache of instantiated renderer instances.
     * @psalm-var array<class-string, ColumnRendererInterface>
     */
    private array $cache = [];

    /**
     * @var array Configuration settings for renderer classes.
     * @psalm-var array<class-string, array>
     */
    private array $configs = [];

    /**
     * @param ContainerInterface $container The dependency injection container for creating renderers.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->injector = new Injector($container);
    }

    /**
     * Get a configured instance of a column renderer.
     *
     * If the renderer instance is not in cache, it will be created using the dependency injector
     * and configured with settings provided via {@see addConfigs()}.
     *
     * @param string $class The class name of the renderer to instantiate.
     *
     * @return ColumnRendererInterface The configured renderer instance.
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
     * Add configuration settings for column renderers.
     *
     * This method allows configuring multiple renderer classes at once.
     * For each class, you can provide constructor arguments either by name or position.
     *
     * @param array $configs Configuration settings for renderers.
     * Keys are renderer class names, values are arrays of constructor arguments.
     *
     * @return self New instance with updated configurations.
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
