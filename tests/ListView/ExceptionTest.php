<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Exception\DataReaderNotSetException;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ExceptionTest extends TestCase
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
    public function testGetPaginator(): void
    {
        $this->expectException(DataReaderNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "dataReader" is not set.');
        ListView::widget()
            ->itemView('//_listview')
            ->render();
    }

    public function testItemViewWithNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "itemView" property must be set.');
        ListView::widget()
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();
    }

    public function testEmptyItemViewTag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "itemViewTag" property cannot be empty.');
        ListView::widget()
            ->itemViewTag('')
            ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();
    }

    public function testEmptyItemsWrapperTag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "itemsWrapperTag" property cannot be empty.');
        ListView::widget()
            ->itemsWrapperTag('')
            ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();
    }
}
