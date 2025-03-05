<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Yii\DataView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Column\Base\HeaderContext;
use Yiisoft\Yii\DataView\Column\Base\RendererContainer;
use Yiisoft\Yii\DataView\Column\ColumnInterface;
use Yiisoft\Yii\DataView\Column\ColumnRendererInterface;

/**
 * @covers \Yiisoft\Yii\DataView\Column\Base\RendererContainer
 */
final class RendererContainerTest extends TestCase
{
    private TestRenderer $renderer;
    private ContainerInterface $container;
    private RendererContainer $rendererContainer;

    protected function setUp(): void
    {
        $this->renderer = new TestRenderer();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container
            ->method('get')
            ->willReturnCallback(fn(string $class) => match ($class) {
                TestRenderer::class => $this->renderer,
                default => throw new \RuntimeException("Unexpected class: $class"),
            });
        $this->rendererContainer = new RendererContainer($this->container);
    }

    public function testGetCreatesNewInstance(): void
    {
        $instance = $this->rendererContainer->get(TestRenderer::class);
        $this->assertInstanceOf(TestRenderer::class, $instance);
    }

    public function testGetReturnsCachedInstance(): void
    {
        $instance1 = $this->rendererContainer->get(TestRenderer::class);
        $instance2 = $this->rendererContainer->get(TestRenderer::class);
        $this->assertSame($instance1, $instance2);
    }

    public function testAddConfigsCreatesNewInstanceWithConfig(): void
    {
        $container = $this->rendererContainer->addConfigs([
            TestRenderer::class => ['value' => 'custom'],
        ]);

        $instance = $container->get(TestRenderer::class);
        $this->assertSame('custom', $instance->getValue());
    }

    public function testAddConfigsMergesExistingConfig(): void
    {
        $container = $this->rendererContainer
            ->addConfigs([TestRenderer::class => ['value' => 'first']])
            ->addConfigs([TestRenderer::class => ['option' => 'second']]);

        $instance = $container->get(TestRenderer::class);
        $this->assertSame('first', $instance->getValue());
        $this->assertSame('second', $instance->getOption());
    }
}

/**
 * Test renderer implementation.
 */
final class TestRenderer implements ColumnRendererInterface
{
    public function __construct(
        private string $value = 'default',
        private string $option = 'default',
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOption(): string
    {
        return $this->option;
    }

    public function renderColumn(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell;
    }

    public function renderHeader(ColumnInterface $column, Cell $cell, HeaderContext $context): ?Cell
    {
        return $cell;
    }

    public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
    {
        return $cell;
    }

    public function renderFooter(ColumnInterface $column, Cell $cell, GlobalContext $context): Cell
    {
        return $cell;
    }
}
