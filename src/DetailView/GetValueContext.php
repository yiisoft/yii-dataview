<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\DetailView;

/**
 * Context for a callable that is passed to {@see DataField::getValue()}.
 */
final class GetValueContext
{
    /**
     * @param DataField $field Data field to get value for.
     * @param array|object $data Data item to get value from.
     */
    public function __construct(
        public readonly DataField $field,
        public readonly array|object $data,
    ) {
    }
}
