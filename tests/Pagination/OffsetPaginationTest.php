<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSetException;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OffsetPaginationTest extends TestCase
{
    use TestTrait;

    private const PAGE_SIZE = 10;
    private const TOTAL_ITEMS = 30;

    /**
     * @return array<int, array{id: int, value: string}>
     */
    private function createTestData(): array
    {
        return array_map(
            static fn (int $i): array => ['id' => $i, 'value' => "Item $i"],
            range(1, self::TOTAL_ITEMS)
        );
    }

    /**
     * @param array<int, array{id: int, value: string}> $data
     */
    private function createGridView(array $data, OffsetPagination $pagination): string
    {
        return GridView::widget()
            ->id('w1-grid')
            ->dataReader($this->createOffsetPaginator($data, self::PAGE_SIZE))
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'value')
            )
            ->paginationWidget($pagination)
            ->render();
    }

    /**
     * @throws CircularReferenceException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws NotFoundException
     */
    public function testEmptyData(): void
    {
        $offsetPaginator = $this->createOffsetPaginator([], self::PAGE_SIZE);

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="0">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->id('w1-grid')
                ->dataReader($offsetPaginator)
                ->paginationWidget(OffsetPagination::widget())
                ->render(),
        );
    }

    public function testPaginatorNotSet(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(PaginatorNotSetException::class);
        $pagination->render();
    }

    public function testEmptyContainerTag(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $pagination->containerTag('');
    }

    public function testEmptyListTag(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $pagination->listTag('');
    }

    public function testEmptyItemTag(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $pagination->itemTag('');
    }

    /**
     * @throws CircularReferenceException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws NotFoundException
     */
    public function testCustomAttributes(): void
    {
        $pagination = OffsetPagination::widget()
            ->containerAttributes(['class' => 'container'])
            ->linkAttributes(['class' => 'link'])
            ->currentLinkClass('current')
            ->disabledLinkClass('inactive')
            ->maxNavLinkCount(5);

        $html = $this->createGridView($this->createTestData(), $pagination);

        $this->assertStringContainsString('class="container"', $html);
        $this->assertStringContainsString('class="link inactive"', $html);
        $this->assertStringContainsString('class="link current"', $html);
    }

    /**
     * @throws CircularReferenceException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws NotFoundException
     */
    public function testCustomLabels(): void
    {
        $pagination = OffsetPagination::widget()
            ->labelFirst('First')
            ->labelLast('Last')
            ->labelPrevious('Prev')
            ->labelNext('Next');

        $html = $this->createGridView($this->createTestData(), $pagination);

        $this->assertStringContainsString('First', $html);
        $this->assertStringContainsString('Last', $html);
        $this->assertStringContainsString('Prev', $html);
        $this->assertStringContainsString('Next', $html);
    }

    /**
     * @throws CircularReferenceException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws NotFoundException
     */
    public function testCustomTags(): void
    {
        $pagination = OffsetPagination::widget()
            ->containerTag('div')
            ->listTag('ul')
            ->itemTag('li');

        $html = $this->createGridView($this->createTestData(), $pagination);

        $this->assertStringContainsString('<div', $html);
        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('<li', $html);
    }
}
