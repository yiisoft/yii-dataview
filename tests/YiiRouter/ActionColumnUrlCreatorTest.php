<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\YiiRouter;

use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\UrlParameterType;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlConfig;
use Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator;

/**
 * @covers \Yiisoft\Yii\DataView\YiiRouter\ActionColumnUrlCreator
 */
final class ActionColumnUrlCreatorTest extends TestCase
{
    private UrlGeneratorInterface $urlGenerator;
    private CurrentRoute $currentRoute;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->currentRoute = new CurrentRoute();
        $this->currentRoute->setRouteWithArguments(Route::get('/test')->name('test'), []);
    }

    public function testInvokeWithDefaultConfig(): void
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'test/view',
                [],
                ['id' => '123']
            )
            ->willReturn('/test/view?id=123');

        $creator = new ActionColumnUrlCreator($this->urlGenerator, $this->currentRoute);

        $column = new ActionColumn();
        $data = ['id' => 123];
        $dataReader = $this->createStub(ReadableDataInterface::class);
        $context = new DataContext($dataReader, $column, $data, 123, 0);

        $url = $creator('view', $context);

        $this->assertSame('/test/view?id=123', $url);
    }

    public function testInvokeWithCustomConfig(): void
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'custom/edit',
                ['user_id' => '456'],
                ['page' => '1']
            )
            ->willReturn('/custom/edit/456?page=1');

        $config = new ActionColumnUrlConfig(
            baseRouteName: 'custom',
            primaryKey: 'user_id',
            primaryKeyParameterType: UrlParameterType::PATH,
            queryParameters: ['page' => '1']
        );

        $creator = new ActionColumnUrlCreator($this->urlGenerator, $this->currentRoute);

        $column = new ActionColumn(urlConfig: $config);
        $data = ['user_id' => 456];
        $dataReader = $this->createStub(ReadableDataInterface::class);
        $context = new DataContext($dataReader, $column, $data, 456, 0);

        $url = $creator('edit', $context);

        $this->assertSame('/custom/edit/456?page=1', $url);
    }

    public function testInvokeWithObjectData(): void
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'test/view',
                [],
                ['id' => '789']
            )
            ->willReturn('/test/view?id=789');

        $creator = new ActionColumnUrlCreator($this->urlGenerator, $this->currentRoute);

        $column = new ActionColumn();
        $data = new class () {
            public int $id = 789;
        };
        $dataReader = $this->createStub(ReadableDataInterface::class);
        $context = new DataContext($dataReader, $column, $data, 789, 0);

        $url = $creator('view', $context);

        $this->assertSame('/test/view?id=789', $url);
    }

    public function testInvokeWithInvalidConfig(): void
    {
        $creator = new ActionColumnUrlCreator($this->urlGenerator, $this->currentRoute);

        $column = new ActionColumn(urlConfig: 'invalid');
        $data = ['id' => 123];
        $dataReader = $this->createStub(ReadableDataInterface::class);
        $context = new DataContext($dataReader, $column, $data, 123, 0);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(ActionColumnUrlCreator::class . ' supports ' . ActionColumnUrlConfig::class . ' only.');

        $creator('view', $context);
    }

    public function testConstructor(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $currentRoute = new CurrentRoute();

        // Test with default values
        $creator = new ActionColumnUrlCreator($urlGenerator, $currentRoute);
        $this->assertInstanceOf(ActionColumnUrlCreator::class, $creator);

        // Test with custom values
        $creator = new ActionColumnUrlCreator(
            $urlGenerator,
            $currentRoute,
            'custom_id',
            UrlParameterType::PATH
        );
        $this->assertInstanceOf(ActionColumnUrlCreator::class, $creator);
    }
}
