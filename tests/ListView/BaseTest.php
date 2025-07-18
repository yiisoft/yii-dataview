<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Paginator\InvalidPageException;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\ListItemContext;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\SimpleUrlParameterProvider;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

use function dirname;

final class BaseTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

    public function testAfterItemBeforeItem(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <span class="testMe">
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            </span>
            <span class="testMe">
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </span>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->afterItem(static fn () => '</span>')
                ->beforeItem(static fn () => '<span class="testMe">')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testAfterItemBeforeItemWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <span class="testMe" data-item-id="1">
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <span data-item-id="1">just for test</span></span>
            <span class="testMe" data-item-id="2">
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            <span data-item-id="2">just for test</span></span>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->afterItem(static fn (ListItemContext $context) => '<span data-item-id="' . $context->data['id'] . '">just for test</span></span>')
                ->beforeItem(static fn (ListItemContext $context) => '<span class="testMe" data-item-id="' . $context->data['id'] . '">')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testItemAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li class="testMe">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li class="testMe">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->itemAttributes(['class' => 'testMe'])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testItemView(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testItemCallback(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div>1</div><div>John</div>
            </li>
            <li>
            <div>2</div><div>Mary</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemCallback(
                    fn (ListItemContext $context) => '<div>' . $context->data['id'] . '</div><div>' . $context->data['name'] . '</div>' . PHP_EOL
                )
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testSeparator(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testViewParameters(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div class=text-success>1</div>
            </li>
            <li>
            <div class=text-success>2</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listviewparams.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->itemViewParameters(['itemClass' => 'text-success'])
                ->render(),
        );
    }

    public function testOffsetPaginationConfig(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div class=text-success>1</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>2</b></div>
            <nav>
            <ul class="pagination">
            <li class="page-item disabled"><a class="page-link" href="#">⟪</a></li>
            <li class="page-item disabled"><a class="page-link" href="#">⟨</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#page=2">2</a></li>
            <li class="page-item"><a class="page-link" href="#page=2">⟩</a></li>
            <li class="page-item"><a class="page-link" href="#page=2">⟫</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listviewparams.php')
                ->dataReader($this->createOffsetPaginator($this->data, 1))
                ->separator(PHP_EOL)
                ->itemViewParameters(['itemClass' => 'text-success'])
                ->offsetPaginationConfig([
                    'listTag()' => ['ul'],
                    'listAttributes()' => [['class' => 'pagination']],
                    'itemTag()' => ['li'],
                    'itemAttributes()' => [['class' => 'page-item']],
                    'linkAttributes()' => [['class' => 'page-link']],
                    'currentItemClass()' => ['active'],
                    'disabledItemClass()' => ['disabled'],
                ])
                ->render(),
        );
    }

    public function testKeysetPaginationConfig(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div class=text-success>1</div>
            </li>
            </ul>

            <nav>
            <ul class="pagination">
            <li class="page-item disabled"><a class="page-link">⟨</a></li>
            <li class="page-item"><a class="page-link" href="#page=1">⟩</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listviewparams.php')
                ->dataReader($this->createKeysetPaginator($this->data, 1))
                ->separator(PHP_EOL)
                ->itemViewParameters(['itemClass' => 'text-success'])
                ->keysetPaginationConfig([
                    'listTag()' => ['ul'],
                    'listAttributes()' => [['class' => 'pagination']],
                    'itemTag()' => ['li'],
                    'itemAttributes()' => [['class' => 'page-item']],
                    'linkAttributes()' => [['class' => 'page-link']],
                    'disabledItemClass()' => ['disabled'],
                ])
                ->render(),
        );
    }

    public function testItemListTag(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ol>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ol>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemListTag('ol')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testItemAndItemListTag(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </div>
            <div>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </div>
            </div>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemListTag('div')
                ->itemTag('div')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testItemListAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul class="the-item-wrapper-class">
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemListAttributes(['class' => 'the-item-wrapper-class'])
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testNoItemListTag(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </div>
            <div>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </div>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemListTag(null)
                ->itemTag('div')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testNoItemTag(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>

            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>

            </div>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemListTag('div')
                ->itemTag(null)
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testNoItemTagNoItemListTag(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>

            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>

            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemListTag(null)
                ->itemTag(null)
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testClosureForItemAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li data-item-id="1" data-item-key="0" data-item-index="0">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li data-item-id="2" data-item-key="1" data-item-index="1">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->itemAttributes(static fn (ListItemContext $context) => [
                    'data-item-id' => $context->data['id'],
                    'data-item-key' => $context->key,
                    'data-item-index' => $context->index,
                ])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    public function testItemAttributesWithSubClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li class="id-1-key-0-index-0">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li class="id-2-key-1-index-1">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->itemAttributes([
                    'class' => static fn(ListItemContext $context) => "id-{$context->data['id']}-key-{$context->key}-index-{$context->index}",
                ])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    public function testSubClosureForItemAttributesWithClosure(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li class="id-1-key-0-index-0">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li class="id-2-key-1-index-1">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->itemAttributes(static fn (ListItemContext $context) => [
                    'class' => static fn(ListItemContext $context) => "id-{$context->data['id']}-key-{$context->key}-index-{$context->index}",
                ])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    public function testIgnoreMissingPageTrue(): void
    {
        $params = new SimpleUrlParameterProvider([
            'page' => '-1',
        ]);

        $result = ListView::widget()
            ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
            ->dataReader($this->createOffsetPaginator($this->data, 1))
            ->ignoreMissingPage(true)
            ->urlParameterProvider($params)
            ->render();

        $this->assertIsString($result);
    }

    public function testIgnoreMissingPageFalse(): void
    {
        $params = new SimpleUrlParameterProvider([
            'page' => '-1',
        ]);

        $listView = ListView::widget()
            ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
            ->dataReader($this->createOffsetPaginator($this->data, 1))
            ->ignoreMissingPage(false)
            ->urlParameterProvider($params);

        $this->expectException(InvalidPageException::class);
        $this->expectExceptionMessage('Current page should be at least 1.');

        $listView->render();
    }

    public function testContainerClass(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div class="my container">
            <ul>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->containerAttributes(['class' => 'existing'])
                ->containerClass('my', 'container')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    public function testAddContainerClass(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div class="existing my container">
            <ul>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->containerAttributes(['class' => 'existing'])
                ->addContainerClass('my', 'container')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    public function testThrowExceptionForContainterTagEmptyValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');

        ListView::widget()->containerTag('');
    }

    public function testThrowExceptionForPageSizeTagEmptyValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');

        ListView::widget()->pageSizeTag('');
    }

    public function testThrowExceptionFotSummaryTagEmptyValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');

        ListView::widget()->summaryTag('');
    }

    public function testPrependAndAppendContentAreRenderedAroundListView(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div>PRE</div>
            <ul>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <div>Page <b>1</b> of <b>1</b></div>
            <div>POST</div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->prepend('<div>PRE</div>')
                ->append('<div>POST</div>')
                ->render(),
        );
    }

    public function testCustomSummaryTagIsRenderedInListView(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            <strong>Page <b>1</b> of <b>1</b></strong>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->summaryTag('strong')
                ->render(),
        );
    }

    public function testSummaryIsRenderedWithoutTagWhenCustomSummaryTagIsNull(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <ul>
            <li>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </li>
            <li>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </li>
            </ul>
            Page <b>1</b> of <b>1</b>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->summaryTag(null)
                ->render(),
        );
    }
}
