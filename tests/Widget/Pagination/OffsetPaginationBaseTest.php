<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\ExceptionotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Column;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\OffsetPagination;

final class OffsetPaginationBaseTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRenderPaginatorEmptyData(): void
    {
        $offsetPaginator = $this->createOffsetPaginator([], 10);

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table class="table">
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="0">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->columns([])
                ->id('w1-grid')
                ->paginator($offsetPaginator)
                ->pagination(
                    OffsetPagination::widget()
                        ->paginator($offsetPaginator)
                        ->urlGenerator(Mock::urlGenerator())
                        ->render()
                )
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
                ->render(),
        );
    }
}
