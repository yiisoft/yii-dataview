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
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class BaseTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
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
            <div>dataview.summary</div>
            </div>
            HTML,
            ListView::widget()
                ->afterItem(static fn () => '</span>')
                ->beforeItem(static fn () => '<span class="testMe">')
                ->itemView('//_listview')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->webView(Mock::webView())
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
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
            <div>dataview.summary</div>
            </div>
            HTML,
            ListView::widget()
                ->itemView('//_listview')
                ->itemViewAttributes(['class' => 'testMe'])
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->webView(Mock::webView())
                ->render(),
        );
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
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
            <div>dataview.summary</div>
            </div>
            HTML,
            ListView::widget()
                ->itemView('//_listview')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->webView(Mock::webView())
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
            <div>dataview.summary</div>
            </div>
            HTML,
            ListView::widget()
                ->itemView(
                    fn (array $data) => '<div>' . $data['id'] . '</div><div>' . $data['name'] . '</div>' . PHP_EOL
                )
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->webView(Mock::webView())
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
            <div>dataview.summary</div>
            </div>
            HTML,
            ListView::widget()
                ->itemView('//_listview')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->webView(Mock::webView())
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
            <div>dataview.summary</div>
            </div>
            HTML,
            ListView::widget()
                ->itemView('//_listviewparams')
                ->dataReader($this->createOffsetPaginator($this->data, 10))
                ->separator(PHP_EOL)
                ->webView(Mock::webView())
                ->viewParams(['itemClass' => 'text-success'])
                ->render(),
        );
    }
}
