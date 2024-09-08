<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
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
            <span class="testMe">
            <div>
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </div>
            </span>
            <span class="testMe">
            <div>
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </div>
            </span>
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

    public function testItemViewAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div class="testMe">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </div>
            <div class="testMe">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </div>
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
            <div>
            <div>1</div><div>John</div>
            </div>
            <div>
            <div>2</div><div>Mary</div>
            </div>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(
                    fn (array $data) => '<div>' . $data['id'] . '</div><div>' . $data['name'] . '</div>' . PHP_EOL
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
            <div>
            <div class=text-success>1</div>
            </div>
            <div>
            <div class=text-success>2</div>
            </div>
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
            <div>
            <div class=text-success>1</div>
            </div>
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
            <div>
            <div class=text-success>1</div>
            </div>

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

    public function testClosureForItemViewAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div>
            <div data-item-id="1" data-item-key="0" data-item-index="0">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </div>
            <div data-item-id="2" data-item-key="1" data-item-index="1">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </div>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->itemViewAttributes(static fn (array $data, $key, $index, $widget) => [
                    'data-item-id' => $data['id'],
                    'data-item-key' => $key,
                    'data-item-index' => $index,
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
            <div class="id-1-key-0-index-0">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </div>
            <div class="id-2-key-1-index-1">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </div>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->itemViewAttributes([
                    'class' => static fn(array $data, $key, $index) => "id-{$data['id']}-key-{$key}-index-{$index}",
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
            <div class="id-1-key-0-index-0">
            <div>Id: 1</div><div>Name: John</div><div>Age: 20</div>
            </div>
            <div class="id-2-key-1-index-1">
            <div>Id: 2</div><div>Name: Mary</div><div>Age: 21</div>
            </div>
            <div>Page <b>1</b> of <b>1</b></div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
                ->itemViewAttributes(static fn (array $data, $key, $index) => [
                    'class' => static fn(array $data, $key, $index) => "id-{$data['id']}-key-{$key}-index-{$index}",
                ])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->render(),
        );
    }
}
