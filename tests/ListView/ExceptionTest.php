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
        $this->expectExceptionMessage('Either "itemView" or "itemCallback" must be set.');
        ListView::widget()
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();
    }

    public function testEmptyItemTag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "itemTag" cannot be empty.');
        ListView::widget()
            ->itemTag('')
            ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();
    }

    public function testEmptyItemListTag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "itemListTag" cannot be empty.');
        ListView::widget()
            ->itemListTag('')
            ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->render();
    }

    public function testEmptySummaryTagThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');

        ListView::widget()
            ->itemView(dirname(__DIR__) . '/Support/view/_listview.php')
            ->dataReader($this->createOffsetPaginator($this->data, 10))
            ->summaryTag('')
            ->render();
    }
}
