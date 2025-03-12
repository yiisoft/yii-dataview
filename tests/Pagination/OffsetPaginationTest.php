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

    /**
     * @throws CircularReferenceException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws NotFoundException
     */
    public function testRenderPaginatorEmptyData(): void
    {
        $offsetPaginator = $this->createOffsetPaginator([], 10);

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

    public function testNotSetPaginator(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
        Assert::invokeMethod($pagination, 'getPaginator');
    }

    public function testContainerTagCannotBeEmpty(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $pagination->containerTag('');
    }

    public function testListTagCannotBeEmpty(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        $pagination->listTag('');
    }

    public function testItemTagCannotBeEmpty(): void
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
    public function testCustomAttributesAreRendered(): void
    {
        $data = array_map(
            static fn (int $i): array => ['id' => $i, 'value' => "Item $i"],
            range(1, 30)
        );
        $offsetPaginator = $this->createOffsetPaginator($data, 10);

        $html = GridView::widget()
            ->id('w1-grid')
            ->dataReader($offsetPaginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'value')
            )
            ->paginationWidget(
                OffsetPagination::widget()
                    ->containerAttributes(['class' => 'container'])
                    ->listAttributes(['class' => 'list'])
                    ->itemAttributes(['class' => 'item'])
                    ->linkAttributes(['class' => 'link'])
                    ->currentItemClass('active')
                    ->disabledItemClass('disabled')
                    ->currentLinkClass('current')
                    ->disabledLinkClass('inactive')
                    ->maxNavLinkCount(5)
            )
            ->render();

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
    public function testCustomLabelsAreRendered(): void
    {
        $data = array_map(
            static fn (int $i): array => ['id' => $i, 'value' => "Item $i"],
            range(1, 30)
        );
        $offsetPaginator = $this->createOffsetPaginator($data, 10);

        $html = GridView::widget()
            ->id('w1-grid')
            ->dataReader($offsetPaginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'value')
            )
            ->paginationWidget(
                OffsetPagination::widget()
                    ->labelFirst('First')
                    ->labelLast('Last')
                    ->labelPrevious('Prev')
                    ->labelNext('Next')
            )
            ->render();

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
    public function testCustomTagsAreRendered(): void
    {
        $data = array_map(
            static fn (int $i): array => ['id' => $i, 'value' => "Item $i"],
            range(1, 30)
        );
        $offsetPaginator = $this->createOffsetPaginator($data, 10);

        $html = GridView::widget()
            ->id('w1-grid')
            ->dataReader($offsetPaginator)
            ->columns(
                new DataColumn(property: 'id'),
                new DataColumn(property: 'value')
            )
            ->paginationWidget(
                OffsetPagination::widget()
                    ->containerTag('div')
                    ->listTag('ul')
                    ->itemTag('li')
            )
            ->render();

        $this->assertStringContainsString('<div', $html);
        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('<li', $html);
    }
}
