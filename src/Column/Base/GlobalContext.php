<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Data\Reader\ReadableDataInterface;

final class GlobalContext
{
    public function __construct(
        public readonly ReadableDataInterface $dataReader,
        public readonly array $sortLinkAttributes,
        public readonly array $urlArguments = [],
        public readonly array $urlQueryParameters = [],
        public readonly ?string $filterModelName = null,
    ) {
    }
}
