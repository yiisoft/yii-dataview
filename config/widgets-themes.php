<?php

declare(strict_types=1);

use Yiisoft\Yii\DataView\Column\ActionColumnRenderer;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\Filter\Widget\TextInputFilter;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\KeysetPagination;
use Yiisoft\Yii\DataView\OffsetPagination;

return [
    'bootstrap5' => [
        GridView::class => [
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
            'currentItemClass()' => ['active'],
            'disabledItemClass()' => ['disabled'],
        ],
    ],
];
