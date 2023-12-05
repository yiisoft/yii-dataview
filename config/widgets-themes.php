<?php

declare(strict_types=1);

use Yiisoft\Yii\DataView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView;

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
    ],
];
