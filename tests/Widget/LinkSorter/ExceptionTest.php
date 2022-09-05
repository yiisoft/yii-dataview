<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\LinkSorter;

use InvalidArgumentException;
use ReflectionException;
use Yiisoft\Yii\DataView\Tests\TestCase;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

final class ExceptionTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testUrlGeneratorNotSet(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('UrlGenerator must be configured.');
        LinkSorter::widget()
            ->attribute('username')
            ->attributes([
                'username' => [
                    'asc' => ['username' => SORT_ASC],
                    'desc' => ['username' => SORT_DESC],
                    'label' => 'Username',
                ],
            ])
            ->render();
    }
}
