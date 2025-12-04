<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;

use function is_object;

final class SimpleActionColumnUrlCreator
{
    public function __construct(
        private readonly string $primaryKey = 'id',
    ) {}

    public function __invoke(string $action, DataContext $context): string
    {
        /** @var ActionColumn $column */
        $column = $context->column;

        /** @var array $config */
        $config = $column->urlConfig;

        $primaryKey = $config['primaryKey'] ?? $this->primaryKey;

        $primaryKeyValue = is_object($context->data)
            ? $context->data->$primaryKey
            : $context->data[$primaryKey];

        return '/' . $action . '/' . $primaryKeyValue;
    }
}
