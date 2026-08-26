<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * Trait providing an implementation of {@see UseInlineJsInterface}.
 *
 * @psalm-require-implements UseInlineJsInterface
 */
trait UseInlineJsTrait
{
    private bool $useInlineJs = true;

    /**
     * @see UseInlineJsInterface::useInlineJs()
     */
    public function useInlineJs(bool $enabled): static
    {
        $new = clone $this;
        $new->useInlineJs = $enabled;
        return $new;
    }
}
