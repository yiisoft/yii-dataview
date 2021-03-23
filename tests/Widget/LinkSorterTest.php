<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget;

use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\Tests\TestCase;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

final class LinkSorterTest extends TestCase
{
    public function testFrameworkCssException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Invalid framework css. Valid values are: "bootstrap", "bulma".');
        LinkSorter::widget()->frameworkCss('NoExist');
    }
}
