<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\LinkSorter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\LinkSorter;

final class ExceptionTest extends TestCase
{
    use TestTrait;

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
