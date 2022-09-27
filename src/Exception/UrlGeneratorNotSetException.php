<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Exception;

use RuntimeException;
use Throwable;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class UrlGeneratorNotSetException extends RuntimeException implements FriendlyExceptionInterface
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = $message === '' ? $this->getName() : $message;
        parent::__construct($message, $code, $previous);
    }

    public function getName(): string
    {
        return 'Failed to create widget because "urlgenerator" is not set.';
    }

    public function getSolution(): ?string
    {
        return <<<SOLUTION
            You can configure the `urlGenerator` property in the widget configuration. Use
            `UrlGeneratorInterface::class`.
            For more information [see the router documentation](https://github.com/yiisoft/router).
        SOLUTION;
    }
}
