<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use LogicException;

/**
 * @psalm-require-implements PaginationControlInterface
 */
trait PaginationContextTrait
{
    private ?PaginationContext $context = null;

    final public function withContext(PaginationContext $context): static
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

    final protected function getContext(): PaginationContext
    {
        if ($this->context === null) {
            throw new LogicException('Context is not set.');
        }
        return $this->context;
    }
}
