<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column\Base;

use PHPUnit\Framework\TestCase;
use Stringable;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\Column\Base\GlobalContext;

/**
 * @covers \Yiisoft\Yii\DataView\Column\Base\GlobalContext
 */
final class GlobalContextTest extends TestCase
{
    public function testConstructor(): void
    {
        $dataReader = $this->createMock(ReadableDataInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);
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
        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('translate')
            ->with('test.message', [], 'app')
            ->willReturn('Test Message');

        $context = new GlobalContext(
            $this->createMock(ReadableDataInterface::class),
            [],
            [],
            $translator,
            'app'
        );

        $this->assertSame('Test Message', $context->translate('test.message'));
    }

    public function testTranslateWithStringable(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'test.stringable';
            }
        };

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('translate')
            ->with('test.stringable', [], 'app')
            ->willReturn('Test Stringable');

        $context = new GlobalContext(
            $this->createMock(ReadableDataInterface::class),
            [],
            [],
            $translator,
            'app'
        );

        $this->assertSame('Test Stringable', $context->translate($stringable));
    }
}
