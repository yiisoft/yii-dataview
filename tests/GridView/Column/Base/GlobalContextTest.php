<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column\Base;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\InMemoryMessageSource;
use Yiisoft\Translator\Translator;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\GridView\Column\Base\Cell;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;
use Yiisoft\Yii\DataView\Tests\Support\TestHelper;

final class GlobalContextTest extends TestCase
{
    public function testTranslate(): void
    {
        $messageSource = new InMemoryMessageSource();
        $messageSource->write(
            BaseListView::DEFAULT_TRANSLATION_CATEGORY,
            'en',
            ['test.message' => 'Translated Message'],
        );
        $translator = (new Translator('en'))
            ->addCategorySources(
                new CategorySource(BaseListView::DEFAULT_TRANSLATION_CATEGORY, $messageSource)
            );
        $context = TestHelper::createGlobalContext(translator: $translator);

        $this->assertSame('Translated Message', $context->translate('test.message'));
        $this->assertSame('Translated Message', $context->translate(new StringableObject('test.message')));
    }

    public function testPrepareSortable(): void
    {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: Sort::any(['name']),
            originalSort: Sort::any(['name']),
            allowedProperties: ['name'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '↕',
            sortableHeaderAppend: '!',
        );

        $result = $context->prepareSortable($cell, 'name');

        $this->assertSame(
            [
                ['class' => 'sortable'],
                '<a href="/route?sort=name"></a>',
                '↕',
                '!',
            ],
            [
                $result[0]->getAttributes(),
                $result[1]?->render(),
                $result[2],
                $result[3],
            ],
        );
    }

    #[TestWith(['sort=-name', 'name', ''])]
    #[TestWith(['', 'name', '-name'])]
    #[TestWith(['sort=-name', 'name', 'name'])]
    public function testPrepareSortableAsc(
        string $expectedUrlSort,
        string $order,
        string $originalOrder,
    ): void {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: Sort::any(['name'])->withOrderString($order),
            originalSort: Sort::any(['name'])->withOrderString($originalOrder),
            allowedProperties: ['name'],
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '↑',
            sortableHeaderAscAppend: '!',
            sortableLinkAscClass: 'link-asc'
        );

        $result = $context->prepareSortable($cell, 'name');

        $this->assertSame(
            [
                ['class' => 'asc'],
                '<a class="link-asc" href="/route?' . $expectedUrlSort . '"></a>',
                '↑',
                '!',
            ],
            [
                $result[0]->getAttributes(),
                $result[1]?->render(),
                $result[2],
                $result[3],
            ],
        );
    }

    public function testPrepareSortableDesc(): void
    {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: Sort::any(['name'])->withOrder(['name' => 'desc']),
            originalSort: Sort::any(['name']),
            allowedProperties: ['name'],
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '↓',
            sortableHeaderDescAppend: '!',
            sortableLinkDescClass: 'link-desc'
        );

        $result = $context->prepareSortable($cell, 'name');

        $this->assertSame(
            [
                ['class' => 'desc'],
                '<a class="link-desc" href="/route?"></a>',
                '↓',
                '!',
            ],
            [
                $result[0]->getAttributes(),
                $result[1]?->render(),
                $result[2],
                $result[3],
            ],
        );
    }

    public function testPrepareSortableMultisort(): void
    {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: Sort::any(['name', 'age'])->withOrder(['name' => 'asc']),
            originalSort: Sort::any(['name', 'age']),
            allowedProperties: ['name', 'age'],
            sortableHeaderClass: 'sortable',
            sortableHeaderPrepend: '↕',
            sortableHeaderAppend: '!',
            multiSort: true
        );

        $result = $context->prepareSortable($cell, 'age');

        $this->assertSame(
            [
                ['class' => 'sortable'],
                '<a href="/route?sort=name%2Cage"></a>',
                '↕',
                '!',
            ],
            [
                $result[0]->getAttributes(),
                $result[1]?->render(),
                $result[2],
                $result[3],
            ],
        );
    }

    #[TestWith(['sort=name%2C-age', ''])]
    #[TestWith(['', 'name,-age'])]
    public function testPrepareSortableMultisortAsc(string $expectedUrlSort, string $originalOrder): void
    {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: Sort::any(['name', 'age'])->withOrder(['name' => 'asc', 'age' => 'asc']),
            originalSort: Sort::any(['name', 'age'])->withOrderString($originalOrder),
            allowedProperties: ['name', 'age'],
            sortableHeaderAscClass: 'asc',
            sortableHeaderAscPrepend: '↑',
            sortableHeaderAscAppend: '!',
            sortableLinkAscClass: 'link-asc',
            multiSort: true
        );

        $result = $context->prepareSortable($cell, 'age');

        $this->assertSame(
            [
                ['class' => 'asc'],
                '<a class="link-asc" href="/route?' . $expectedUrlSort . '"></a>',
                '↑',
                '!',
            ],
            [
                $result[0]->getAttributes(),
                $result[1]?->render(),
                $result[2],
                $result[3],
            ],
        );
    }

    #[TestWith(['sort=name', 'name,-age', ''])]
    #[TestWith(['', 'name,-age', 'name'])]
    #[TestWith(['sort=age', '-age', 'name,age'])]
    #[TestWith(['', '-age', ''])]
    public function testPrepareSortableMultisortDesc(
        string $expectedUrlSort,
        string $order,
        string $originalOrder,
    ): void {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: Sort::any(['name', 'age'])->withOrderString($order),
            originalSort: Sort::any(['name', 'age'])->withOrderString($originalOrder),
            allowedProperties: ['name', 'age'],
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '↓',
            sortableHeaderDescAppend: '!',
            sortableLinkDescClass: 'link-desc',
            multiSort: true
        );

        $result = $context->prepareSortable($cell, 'age');

        $this->assertSame(
            [
                ['class' => 'desc'],
                '<a class="link-desc" href="/route?' . $expectedUrlSort . '"></a>',
                '↓',
                '!',
            ],
            [
                $result[0]->getAttributes(),
                $result[1]?->render(),
                $result[2],
                $result[3],
            ],
        );
    }

    public function testPrepareSortableEdgeCase1(): void
    {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: Sort::any(['name', 'age'])->withOrder(['name' => 'desc']),
            originalSort: Sort::any(['name', 'age'])->withOrder(['age' => 'asc']),
            allowedProperties: ['name', 'age'],
            sortableHeaderDescClass: 'desc',
            sortableHeaderDescPrepend: '↓',
            sortableHeaderDescAppend: '!',
            sortableLinkDescClass: 'link-desc'
        );

        $result = $context->prepareSortable($cell, 'name');

        $this->assertSame(
            [
                ['class' => 'desc'],
                '<a class="link-desc" href="/route?"></a>',
                '↓',
                '!',
            ],
            [
                $result[0]->getAttributes(),
                $result[1]?->render(),
                $result[2],
                $result[3],
            ],
        );
    }

    public static function dataPrepareSortableNoSort(): iterable
    {
        yield 'empty property' => [
            'property' => '',
        ];

        yield 'null sort' => [
            'property' => 'name',
            'sort' => null,
            'originalSort' => null,
        ];

        yield 'property not in allowed' => [
            'property' => 'age',
            'sort' => Sort::any(),
            'originalSort' => Sort::any(),
            'allowedProperties' => ['name'],
        ];

        yield 'field not in sort config' => [
            'property' => 'age',
            'sort' => Sort::any(['name' => []]),
            'originalSort' => Sort::any(['name' => []]),
            'allowedProperties' => ['name', 'age'],
        ];
    }

    #[DataProvider('dataPrepareSortableNoSort')]
    public function testPrepareSortableNoSort(
        string $property,
        ?Sort $sort = null,
        ?Sort $originalSort = null,
        array $allowedProperties = []
    ): void {
        $cell = new Cell();
        $context = TestHelper::createGlobalContext(
            sort: $sort,
            originalSort: $originalSort,
            allowedProperties: $allowedProperties
        );

        $result = $context->prepareSortable($cell, $property);

        $this->assertSame(
            [$cell, null, '', ''],
            $result,
        );
    }
}
