<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Helper;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Helper\Attribute;

final class AttributeTest extends TestCase
{
    public static function dataGetInputName(): array
    {
        return [
            ['', 'age', 'age'],
            ['', 'dates[0]', 'dates[0]'],
            ['Login', '[0]content', 'Login[0][content]'],
            ['Login', '[0]dates[0]', 'Login[0][dates][0]'],
            ['Login', 'age', 'Login[age]'],
            ['Login', 'báz', 'Login[báz]'],
            ['Login', 'dates[0]', 'Login[dates][0]'],
        ];
    }

    /**
     * @param string $formModelName The form model name.
     * @param string $attribute The attribute name.
     * @param string $expected The expected input name.
     */
    #[DataProvider('dataGetInputName')]
    public function testGetInputName(string $formModelName, string $attribute, string $expected): void
    {
        $this->assertSame($expected, Attribute::getInputName($formModelName, $attribute));
    }

    public function testGetInputNameExceptionAttributeNoValid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute name must contain word characters only.');
        Attribute::getInputName('Login', '*username');
    }

    public function testGetInputNameExceptionForTabularInputs(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The form name cannot be empty for tabular inputs.');
        Attribute::getInputName('', '[0]dates[0]');
    }
}
