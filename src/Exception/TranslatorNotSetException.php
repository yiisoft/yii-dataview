<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Exception;

use RuntimeException;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class TranslatorNotSetException extends RuntimeException implements FriendlyExceptionInterface
{
    public function __construct(string $message = '')
    {
        $message = $message === '' ? $this->getName() : $message;
        parent::__construct($message);
    }

    public function getName(): string
    {
        return 'Failed to create widget because "translator" is not set.';
    }

    public function getSolution(): ?string
    {
        return <<<SOLUTION
            You can configure the `translator` property in the widget configuration. Use `TranslatorInterface::class`.
            For more information [see the documentation](https://github.com/yiisoft/translator).
        SOLUTION;
    }
}
