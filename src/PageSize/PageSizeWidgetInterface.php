<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\PageSize;

use Stringable;

/**
 * Interface for widgets that allow users to control the number of items displayed per page.
 *
 * This interface defines the contract for page size widgets, which are responsible for:
 * - Rendering page size controls (inputs, dropdowns, etc.)
 * - Managing page size context
 * - Handling user interactions
 *
 * Implementations should:
 * - Use immutable state management
 * - Support context-based configuration
 * - Render valid HTML controls
 *
 * @see InputPageSize A text input implementation
 * @see SelectPageSize A dropdown implementation
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
