<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Exception;

use RuntimeException;
use Throwable;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class PaginatorNotSetException extends RuntimeException implements FriendlyExceptionInterface
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = $message === '' ? $this->getName() : $message;
        parent::__construct($message, $code, $previous);
    }

    public function getName(): string
    {
        return 'Failed to create widget because "paginator" is not set.';
    }

    public function getSolution(): ?string
    {
        return <<<SOLUTION
            You can configure the `paginator` property in the widget configuration. You can use `OffSetPaginator::class`
            or `KeySetPaginator::class` as paginator for more information see the documentation in the link below.

            @link https://github.com/yiisoft/data
        SOLUTION;
    }
}
