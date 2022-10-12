<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Exception;

use RuntimeException;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class PaginatorNotSetException extends RuntimeException implements FriendlyExceptionInterface
{
    public function __construct(string $message = '')
    {
        $message = $message === '' ? $this->getName() : $message;
        parent::__construct($message);
    }

    public function getName(): string
    {
        return 'Failed to create widget because "paginator" is not set.';
    }

    public function getSolution(): ?string
    {
        return <<<SOLUTION
            You can configure the `paginator` property in the widget configuration. Use either `OffSetPaginator::class`
            or `KeySetPaginator::class` as paginator.
            For more information [see the documentation](https://github.com/yiisoft/data).
        SOLUTION;
    }
}
