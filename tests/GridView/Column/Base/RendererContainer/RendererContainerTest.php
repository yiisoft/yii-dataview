<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column\Base\RendererContainer;

use PHPUnit\Framework\TestCase;
use Yiisoft\Test\Support\Container\SimpleContainer;
use Yiisoft\Yii\DataView\GridView\Column\Base\RendererContainer;

/**
 * @covers \Yiisoft\Yii\DataView\GridView\Column\Base\RendererContainer
 */
final class RendererContainerTest extends TestCase
{
    public function testBase(): void
    {
        $container = new RendererContainer(new SimpleContainer());

        /** @var FakeColumnRenderer $renderer */
        $renderer = $container->get(FakeColumnRenderer::class);

        $this->assertSame('', $renderer->id);
        $this->assertSame('', $renderer->name);
    }

    public function testAddConfig(): void
    {
        $container = new RendererContainer(new SimpleContainer());
        $container = $container->addConfigs([
            FakeColumnRenderer::class => ['name' => 'custom-name'],
        ]);

        /** @var FakeColumnRenderer $renderer */
        $renderer = $container->get(FakeColumnRenderer::class);

        $this->assertSame('', $renderer->id);
        $this->assertSame('custom-name', $renderer->name);
    }

    public function testAddConfigMerge(): void
    {
        $container = new RendererContainer(new SimpleContainer());
        $container = $container
            ->addConfigs([
                FakeColumnRenderer::class => ['id' => 'custom-id'],
            ])
            ->addConfigs([
                FakeColumnRenderer::class => ['name' => 'custom-name'],
            ]);

        /** @var FakeColumnRenderer $renderer */
        $renderer = $container->get(FakeColumnRenderer::class);

        $this->assertSame('custom-id', $renderer->id);
        $this->assertSame('custom-name', $renderer->name);
    }

    public function testImmutability(): void
    {
        $container = new RendererContainer(new SimpleContainer());
        $this->assertNotSame($container, $container->addConfigs([]));
    }
}
