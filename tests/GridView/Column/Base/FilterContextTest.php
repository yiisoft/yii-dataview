<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Yii\DataView\GridView\Column\Base\FilterContext;
use Yiisoft\Yii\DataView\Tests\Support\SimpleUrlParameterProvider;

/**
 * @covers \Yiisoft\Yii\DataView\GridView\Column\Base\FilterContext
 */
final class FilterContextTest extends TestCase
{
    public function testGetQueryValue(): void
    {
        $context = new FilterContext(
            'test-id',
            new Result(),
            'invalid',
            [],
            new SimpleUrlParameterProvider(
                query: ['id' => '1'],
                path: ['id' => '2'],
            )
        );

        $this->assertSame('1', $context->getQueryValue('id'));
        $this->assertNull($context->getQueryValue('non-exists'));
    }
}
