<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\ValuePresenter;

interface ValuePresenterInterface
{
    public function present(mixed $value): string;
}
