<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Stringable;

/**
 * Interface for widgets that allow users to control the number of items displayed per page.
 *
 * @see InputPageSize A text input implementation.
 * @see SelectPageSize A dropdown implementation.
 */
interface PageSizeWidgetInterface extends Stringable
{
    /**
     * Creates a new instance with the specified page size context.
     *
     * @param PageSizeContext $context The page size context to use.
     *
     * @return static New instance with the specified context.
     */
    public function withContext(PageSizeContext $context): static;

    /**
     * Renders the page size control.
     *
     * @return string The rendered HTML for the page size control.
     */
    public function render(): string;
}
