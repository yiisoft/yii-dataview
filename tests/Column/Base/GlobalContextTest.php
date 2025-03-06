<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Stringable;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

/**
 * @covers \Yiisoft\Yii\DataView\Column\Base\GlobalContext
 */
final class GlobalContextTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Mary'],
    ];
    public function testConstructor(): void
    {
        $dataReader = new IterableDataReader($this->data);
        $translator = Mock::translator('en');
        $pathArguments = ['id' => 123];
        $queryParameters = ['sort' => 'name'];
        $translationCategory = 'app';

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

    public function testTranslateWithString(): void
    {
        $translator = Mock::translator('en');

        $context = new GlobalContext(
            new IterableDataReader($this->data),
            [],
            [],
            $translator,
            'app'
        );

        $this->assertSame('test.message', $context->translate('test.message'));
    }

    public function testTranslateWithStringable(): void
    {
        $stringable = new class () implements Stringable {
            public function __toString(): string
            {
                return 'test.stringable';
            }
        };

        $translator = Mock::translator('en');

        $context = new GlobalContext(
            new IterableDataReader($this->data),
            [],
            [],
            $translator,
            'app'
        );

        $this->assertSame('test.stringable', $context->translate($stringable));
    }
}
