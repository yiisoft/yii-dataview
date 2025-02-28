<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Tests\Support\Mock;

/**
 * @covers \Yiisoft\Yii\DataView\Column\Base\GlobalContext
 */
final class GlobalContextTest extends TestCase
{
    public function testConstructor(): void
    {
        $data = [
            ['id' => 1, 'name' => 'John', 'age' => 20],
            ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ];
        $dataReader = new IterableDataReader($data);
        $translator = Mock::translator('en');

        $pathArguments = ['id' => '42'];
        $queryParameters = ['sort' => 'name'];
        $translationCategory = 'grid';

        $context = new GlobalContext(
            $dataReader,
            $pathArguments,
            $queryParameters,
            $translator,
            $translationCategory
        );

        $this->assertSame($dataReader, $context->dataReader);
        $this->assertSame($pathArguments, $context->pathArguments);
        $this->assertSame($queryParameters, $context->queryParameters);
    }

    public function testTranslate(): void
    {
        $data = [
            ['id' => 1, 'name' => 'John', 'age' => 20],
            ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ];
        $dataReader = new IterableDataReader($data);
        $translator = Mock::translator('en');

        $context = new GlobalContext(
            $dataReader,
            [],
            [],
            $translator,
            'grid'
        );

        $result = $context->translate('test.message');

        $this->assertSame('test.message', $result);
    }

    public function testTranslateWithStringable(): void
    {
        $data = [
            ['id' => 1, 'name' => 'John', 'age' => 20],
            ['id' => 2, 'name' => 'Mary', 'age' => 21],
        ];
        $dataReader = new IterableDataReader($data);
        $translator = Mock::translator('en');

        $context = new GlobalContext(
            $dataReader,
            [],
            [],
            $translator,
            'grid'
        );

        $stringable = new class () implements \Stringable {
            public function __toString(): string
            {
                return 'stringable.message';
            }
        };

        $result = $context->translate($stringable);

        $this->assertSame('stringable.message', $result);
    }
}
