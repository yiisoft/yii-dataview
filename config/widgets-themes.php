<?php

declare(strict_types=1);

use Yiisoft\Yii\DataView\Column\ActionColumnRenderer;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Pagination\KeysetPagination;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\PageSize\InputPageSize;
use Yiisoft\Yii\DataView\PageSize\SelectPageSize;

return [
    'bootstrap5' => [
        GridView::class => [
            'layout()' => [
                <<<HTML
                {header}
                {toolbar}
                {items}
                {summary}
                <div class="row">
                    <div class="col-md-8">{pager}</div>
                    <div class="col-md-4 text-end">{pageSize}</div>
                </div>
                HTML
            ],
            'pageSizeTag()' => [null],
            'summaryTag()' => ['p'],
            'summaryAttributes()' => [['class' => 'text-secondary']],
            'tableClass()' => ['table table-bordered'],
            'tbodyClass()' => ['table-group-divider'],
            'sortableHeaderPrepend()' => ['<div class="float-end text-secondary text-opacity-50">⭥</div>'],
            'sortableHeaderAscPrepend()' => ['<div class="float-end fw-bold">⭡</div>'],
            'sortableHeaderDescPrepend()' => ['<div class="float-end fw-bold">⭣</div>'],
            'filterCellAttributes()' => [['class' => 'align-top']],
            'filterCellInvalidClass()' => ['bg-danger bg-opacity-10'],
            'filterErrorsContainerAttributes()' => [['class' => 'text-danger mt-1']],
            'addColumnRendererConfigs()-theme' => [
                [
                    ActionColumnRenderer::class => [
                        'buttonClass' => 'btn btn-outline-secondary',
                    ],
                ],
            ],
        ],
        DetailView::class => [
            'fieldListAttributes()' => [['class' => 'row mx-2']],
            'fieldTemplate()' => ["{label}\n{value}"],
            'labelAttributes()' => [['class' => 'col-sm-3 bg-light h-100 py-2']],
            'valueAttributes()' => [['class' => 'col-sm-9 bg-light h-100 py-2']],
        ],
        DropdownFilter::class => [
            'attributes()' => [['class' => 'form-select']],
        ],
        TextInputFilter::class => [
            'attributes()' => [['class' => 'form-control']],
        ],
        OffsetPagination::class => [
            'listTag()' => ['ul'],
            'listAttributes()' => [['class' => 'pagination']],
            'itemTag()' => ['li'],
            'itemAttributes()' => [['class' => 'page-item']],
            'linkAttributes()' => [['class' => 'page-link']],
            'currentItemClass()' => ['active'],
            'disabledItemClass()' => ['disabled'],
        ],
        KeysetPagination::class => [
            'listTag()' => ['ul'],
            'listAttributes()' => [['class' => 'pagination']],
            'itemTag()' => ['li'],
            'itemAttributes()' => [['class' => 'page-item']],
            'linkAttributes()' => [['class' => 'page-link']],
            'disabledItemClass()' => ['disabled'],
        ],
        InputPageSize::class => [
            'attributes()' => [['class' => 'form-control d-inline text-center mx-2', 'style' => 'width:60px']],
        ],
        SelectPageSize::class => [
            'attributes()' => [['class' => 'form-select w-auto d-inline mx-2']],
        ],
    ],
];
