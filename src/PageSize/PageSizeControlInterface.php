<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Stringable;

interface PageSizeControlInterface extends Stringable
{
    public function withContext(PageSizeContext $context): static;

    public function render(): string;
}
