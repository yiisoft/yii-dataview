<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Widget\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\ExceptionotInstantiableException;
use Yiisoft\FactoryotFoundException;
use Yiisoft\Yii\DataView\Column;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;
use Yiisoft\Yii\DataView\Widget\KeysetPagination;

final class KeysetPaginationBaseTest extends TestCase
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
        $keysetPaginator = $this->createKeysetPaginator([], 10, 1, true);

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
                ->paginator($keysetPaginator)
                ->pagination(
                    KeysetPagination::widget()
                        ->paginator($keysetPaginator)
                        ->urlGenerator(Mock::urlGenerator())
                        ->render()
                )
                ->translator(Mock::translator('en'))
                ->urlGenerator(Mock::urlGenerator())
                ->render(),
        );
    }
}
