<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ValuePresenter;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\DataView\Tests\Support\IntegerEnum;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;
use Yiisoft\Yii\DataView\Tests\Support\StringEnum;
use Yiisoft\Yii\DataView\ValuePresenter\SimpleValuePresenter;

final class SimpleValuePresenterTest extends TestCase
{
    public static function dataPresent(): iterable
    {
        yield 'null' => ['', null];
        yield 'true' => ['True', true];
        yield 'false' => ['False', false];
        yield 'string' => ['hello', 'hello'];
        yield 'empty string' => ['', ''];
        yield 'integer' => ['42', 42];
        yield 'negative integer' => ['-42', -42];
        yield 'zero' => ['0', 0];
        yield 'float value' => ['3.14', 3.14];
        yield 'negative float' => ['-3.14', -3.14];
        yield 'zero float' => ['0', 0.0];
        yield 'DateTime' => ['2023-05-15 14:30:00', new DateTime('2023-05-15 14:30:00')];
        yield 'DateTimeImmutable' => ['2023-05-15 14:30:00', new DateTimeImmutable('2023-05-15 14:30:00')];
        yield 'string enum' => ['RED', StringEnum::RED];
        yield 'integer enum' => ['A', IntegerEnum::A];

        $object = new StringableObject('test');
        yield 'stringable object' => [$object, $object];
    }

    #[DataProvider('dataPresent')]
    public function testPresent(mixed $expected, mixed $value): void
    {
        $presenter = new SimpleValuePresenter();

        $result = $presenter->present($value);

        $this->assertSame($expected, $result);
    }

    public function testConstructorCustomValues(): void
    {
        $presenter = new SimpleValuePresenter(
            'N/A',
            'Yes',
            'No',
            'd/m/Y',
        );

        $this->assertSame('N/A', $presenter->present(null));
        $this->assertSame('Yes', $presenter->present(true));
        $this->assertSame('No', $presenter->present(false));
        $this->assertSame('15/05/2023', $presenter->present(new DateTime('2023-05-15 14:30:00')));
    }

    public static function dataInvalidValue(): iterable
    {
        yield 'stdClass' => [new stdClass()];
        yield 'array' => [['key' => 'value']];
        yield 'resource' => [fopen('php://memory', 'r')];
        yield 'closure' => [fn() => 'test'];
    }

    #[DataProvider('dataInvalidValue')]
    public function testInvalidValue(mixed $value): void
    {
        $presenter = new SimpleValuePresenter();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported value type:');
        $presenter->present($value);
    }
}
