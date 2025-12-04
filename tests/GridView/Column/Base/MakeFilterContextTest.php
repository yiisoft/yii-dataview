<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\GridView\Column\Base\MakeFilterContext;
use Yiisoft\Yii\DataView\Tests\Support\SimpleUrlParameterProvider;

/**
 * @covers \Yiisoft\Yii\DataView\GridView\Column\Base\MakeFilterContext
 */
final class MakeFilterContextTest extends TestCase
{
    public function testGetQueryValue(): void
    {
        $context = new MakeFilterContext(
            new Result(),
            new SimpleUrlParameterProvider(
                query: ['search' => 'test-value'],
                path: ['id' => '123'],
            ),
        );

        $this->assertSame('test-value', $context->getQueryValue('search'));
        $this->assertNull($context->getQueryValue('non-exists'));
    }
}
