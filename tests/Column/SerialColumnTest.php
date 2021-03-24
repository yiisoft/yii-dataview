<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Column;

use Yiisoft\Yii\DataView\Columns\SerialColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Tests\TestCase;

final class SerialColumnTest extends TestCase
{
    public function testRender(): void
    {
        GridView::counter(0);

        $columns = [
            [
                '__class' => SerialColumn::class,
                'header()' => ['x'],
            ],
        ];

        $gridView = $this->createGridView($columns);
        $gridView = $gridView->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-gridview" class="grid-view"><div>Showing <b>1-5</b> of <b>6</b> items</div>
        <table class="table table-striped table-bordered">
        <thead>
        <tr><th>x</th></tr>
        </thead>
        <tbody>
        <tr><td>1</td></tr>
        <tr><td>2</td></tr>
        <tr><td>3</td></tr>
        <tr><td>4</td></tr>
        <tr><td>5</td></tr>
        </tbody>
        </table>
        <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
        <li class="page-item disabled"><a class="page-link" href="" data-page="1" aria-disabled="true" tabindex="-1">Previous</a></li>
        <li class="page-item active"><a class="page-link" href="" data-page="1">1</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">2</a></li>
        <li class="page-item"><a class="page-link" href="" data-page="2">Next Page</a></li>
        </ul>
        </nav>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $gridView->render());
    }
}
