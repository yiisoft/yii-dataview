<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\DataView\ListView\ListItemContext;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\SimpleReadable;
use InvalidArgumentException;
use Yiisoft\Yii\DataView\Tests\Support\SimpleUrlParameterProvider;

final class ListViewTest extends TestCase
{
    public function testBase(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Eva'],
        ])
            ->itemView(static fn(array $data): string => $data['id'] . '. ' . $data['name'])
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <ul>
            <li>1. Anna</li>
            <li>2. Eva</li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            $html,
        );
    }

    public function testReadableOnly(): void
    {
        $html = $this->createListView(
            new SimpleReadable([
                ['id' => 1, 'name' => 'Anna'],
                ['id' => 2, 'name' => 'Eva'],
            ])
        )
            ->itemView(static fn(array $data): string => $data['id'] . '. ' . $data['name'])
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <ul>
            <li>1. Anna</li>
            <li>2. Eva</li>
            </ul>
            </div>
            HTML,
            $html,
        );
    }

    public function testWithoutItemView(): void
    {
        $widget = $this->createListView([['id' => 1]]);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('"itemView" must be set.');
        $widget->render();
    }

    public function testListTag(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->listTag('div')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <div>
            <li>Anna</li>
            <li>Bob</li>
            </div>
            HTML,
            $html,
        );
    }

    public function testWithoutListTag(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->listTag(null)
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <li>Anna</li>
            <li>Bob</li>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            $html,
        );
    }

    public function testListTagWithEmptyString(): void
    {
        $widget = new ListView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->listTag('');
    }

    public function testListAttributes(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
        ])
            ->listAttributes(['class' => 'custom-list', 'id' => 'my-list'])
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            '<ul id="my-list" class="custom-list">',
            $html,
        );
    }

    public function testItemTag(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->itemTag('div')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <div>Anna</div>
            <div>Bob</div>
            </ul>
            HTML,
            $html,
        );
    }

    public function testWithoutItemTag(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->itemTag(null)
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            Anna
            Bob
            </ul>
            HTML,
            $html,
        );
    }

    public function testItemTagWithEmptyString(): void
    {
        $widget = new ListView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->itemTag('');
    }

    public function testItemAttributes(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->itemAttributes(['class' => 'list-item', 'data-test' => 'item'])
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <li class="list-item" data-test="item">Anna</li>
            <li class="list-item" data-test="item">Bob</li>
            </ul>
            HTML,
            $html,
        );
    }

    public function testItemAttributesClosure(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->itemAttributes(
                static fn(array $data, ListItemContext $context) => ['class' => 'list-item-' . $data['id']]
            )
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <li class="list-item-1">Anna</li>
            <li class="list-item-2">Bob</li>
            </ul>
            HTML,
            $html,
        );
    }

    public function testBeforeItem(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->beforeItem('<!-- before item -->')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <!-- before item --><li>Anna</li>
            <!-- before item --><li>Bob</li>
            HTML,
            $html,
        );
    }

    public function testAfterItem(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->afterItem('<!-- after item -->')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <li>Anna</li><!-- after item -->
            <li>Bob</li><!-- after item -->
            HTML,
            $html,
        );
    }

    public function testSeparator(): void
    {
        $html = $this->createListView([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ])
            ->separator('<li>---</li>')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <li>Anna</li><li>---</li><li>Bob</li>
            </ul>
            HTML,
            $html,
        );
    }

    public function testNoResultsTag(): void
    {
        $html = $this->createListView()
            ->noResultsTag('b')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            '<b>No results found.</b>',
            $html,
        );
    }

    public function testWithoutNoResultsTag(): void
    {
        $html = $this->createListView()
            ->noResultsTag(null)
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertSame(
            "<div>\nNo results found.\n</div>",
            $html,
        );
    }

    public function testNoResultsTagWithEmptyString(): void
    {
        $widget = new ListView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $widget->noResultsTag('');
    }

    public function testNoResultsAttributes(): void
    {
        $html = $this->createListView()
            ->noResultsAttributes(['class' => 'empty-state', 'data-test' => 'empty'])
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertStringContainsString(
            '<p class="empty-state" data-test="empty">No results found.</p>',
            $html,
        );
    }

    public function testEmptyNoResults(): void
    {
        $html = $this->createListView()
            ->noResultsText('')
            ->itemView(static fn(array $data): string => $data['name'])
            ->render();

        $this->assertSame(
            "<div>\n\n</div>",
            $html,
        );
    }

    public static function dataWithSortFromUrl(): iterable
    {
        yield [
            <<<HTML
            <ul>
            <li>1</li>
            <li>2</li>
            <li>3</li>
            </ul>
            HTML,
            Sort::only(['id']),
        ];
        yield [
            <<<HTML
            <ul>
            <li>1</li>
            <li>2</li>
            <li>3</li>
            </ul>
            HTML,
            Sort::only(['name']),
        ];
    }

    #[DataProvider('dataWithSortFromUrl')]
    public function testWithSortFromUrl(string $expectedList, Sort $sort): void
    {
        $data = [
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
            ['id' => 3, 'name' => 'Charlie'],
        ];
        $dataReader = (new IterableDataReader($data))->withSort($sort);
        $html = (new ListView())
            ->dataReader($dataReader)
            ->urlParameterProvider(new SimpleUrlParameterProvider(['sort' => '-id']))
            ->itemView(static fn(array $data) => $data['id'])
            ->render();

        $this->assertStringContainsString($expectedList, $html);
    }

    private function createListView(ReadableDataInterface|array $data = []): ListView
    {
        $dataReader = $data instanceof ReadableDataInterface ? $data : new IterableDataReader($data);

        return (new ListView())->dataReader($dataReader);
    }
}
