<?php

declare(strict_types=1);

use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\KeysetPagination;
use Yiisoft\Yii\DataView\OffsetPagination;

return [
    'bootstrap5' => [
        GridView::class => [
            'tableClass()' => ['table table-bordered'],
            'tbodyClass()' => ['table-group-divider'],
            'columnsConfigs()' => [
                [
                    ActionColumn::class => [
                        'buttonClass' => 'btn btn-outline-secondary',
                    ],
                ],
            ],
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
