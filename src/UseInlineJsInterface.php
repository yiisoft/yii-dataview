<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

/**
 * Interface for widgets that can toggle inline JavaScript, e.g. an `onchange` attribute, on or off.
 */
interface UseInlineJsInterface
{
    /**
     * Returns a new instance with inline JavaScript enabled or disabled.
     *
     * @param bool $enabled Whether to render inline JavaScript (`true`, the default) or rely on JavaScript
     * registered separately, e.g. to comply with a strict CSP `script-src`.
     *
     * @return static New instance with the specified setting.
     */
    public function useInlineJs(bool $enabled): static;
}
