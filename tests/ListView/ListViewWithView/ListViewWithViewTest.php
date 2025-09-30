<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView\ListViewWithView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Yii\DataView\ListView\ListView;

final class ListViewWithViewTest extends TestCase
{
    public function testBase(): void
    {
        $dataReader = new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ]);

        $html = (new ListView())
            ->dataReader($dataReader)
            ->itemView(__DIR__ . '/base.php')
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <li>(1) Anna</li>
            <li>(2) Bob</li>
            </ul>
            HTML,
            $html,
        );
    }

    public function testExtraParameters(): void
    {
        $dataReader = new IterableDataReader([
            ['id' => 1, 'name' => 'Anna'],
            ['id' => 2, 'name' => 'Bob'],
        ]);

        $html = (new ListView())
            ->dataReader($dataReader)
            ->itemView(__DIR__ . '/extra-parameters.php')
            ->itemViewParameters([
                'header' => 'HELLO',
            ])
            ->render();

        $this->assertStringContainsString(
            <<<HTML
            <ul>
            <li><b>HELLO</b>, Anna</li>
            <li><b>HELLO</b>, Bob</li>
            </ul>
            HTML,
            $html,
        );
    }
}
