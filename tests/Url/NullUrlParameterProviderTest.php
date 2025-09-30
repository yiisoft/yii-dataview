<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Url;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\DataView\Url\NullUrlParameterProvider;
use Yiisoft\Yii\DataView\Url\UrlParameterType;

final class NullUrlParameterProviderTest extends TestCase
{
    public function testBase(): void
    {
        $provider = new NullUrlParameterProvider();

        $result = $provider->get('test', UrlParameterType::PATH);

        $this->assertNull($result);
    }
}
