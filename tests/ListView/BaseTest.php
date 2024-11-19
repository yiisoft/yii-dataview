<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\ListItemContext;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

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

    public function testItemViewAttributes(): void
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
                ->itemViewAttributes(['class' => 'testMe'])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testItemViewAsString(): void
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
    public function testItemViewAsCallable(): void
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
                ->itemView(
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
    public function testViewParams(): void
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
                ->viewParams(['itemClass' => 'text-success'])
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
            <li class="page-item disabled"><a class="page-link" href="#1">⟪</a></li>
            <li class="page-item disabled"><a class="page-link" href="#1">⟨</a></li>
            <li class="page-item active"><a class="page-link" href="#1">1</a></li>
            <li class="page-item"><a class="page-link" href="#2">2</a></li>
            <li class="page-item"><a class="page-link" href="#2">⟩</a></li>
            <li class="page-item"><a class="page-link" href="#2">⟫</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listviewparams.php')
                ->dataReader($this->createOffsetPaginator($this->data, 1))
                ->separator(PHP_EOL)
                ->viewParams(['itemClass' => 'text-success'])
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
            <li class="page-item"><a class="page-link" href="#1">⟩</a></li>
            </ul>
            </nav>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listviewparams.php')
                ->dataReader($this->createKeysetPaginator($this->data, 1))
                ->separator(PHP_EOL)
                ->viewParams(['itemClass' => 'text-success'])
                ->keysetPaginationConfig([
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

    public function testChangeItemsWrapperTag(): void
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
                ->itemsWrapperTag('ol')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testChangeItemsAndItemWrapperTag(): void
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
                ->itemsWrapperTag('div')
                ->itemViewTag('div')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testChangeItemsWrapperTagAttributes(): void
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
                ->itemsWrapperAttributes(['class' => 'the-item-wrapper-class'])
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testNoItemsWrapperTag(): void
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
                ->itemsWrapperTag(null)
                ->itemViewTag('div')
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testNoItemViewTag(): void
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
                ->itemsWrapperTag('div')
                ->itemViewTag(null)
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testNoItemViewTagNoItemsWrapperTag(): void
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
                ->itemsWrapperTag(null)
                ->itemViewTag(null)
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->render(),
        );
    }

    public function testClosureForItemViewAttributes(): void
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
                ->itemViewAttributes(static fn (ListItemContext $context) => [
                    'data-item-id' => $context->data['id'],
                    'data-item-key' => $context->key,
                    'data-item-index' => $context->index,
                ])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    public function testItemViewAttributesWithClosure(): void
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
                ->itemViewAttributes([
                    'class' => static fn(ListItemContext $context) => "id-{$context->data['id']}-key-{$context->key}-index-{$context->index}",
                ])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }

    public function testClosureForItemViewAttributesWithClosure(): void
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
                ->itemViewAttributes(static fn (ListItemContext $context) => [
                    'class' => static fn(ListItemContext $context) => "id-{$context->data['id']}-key-{$context->key}-index-{$context->index}",
                ])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }
}
