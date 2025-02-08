<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Filter\Widget;

use Yiisoft\Widget\Widget;

/**
 * Base class for filter widgets that render filter inputs in data views.
 */
abstract class FilterWidget extends Widget
{
    private Context $context;

    /**
     * Creates a new instance with the specified filter context.
     *
     * @param Context $context The filter context containing property, value, and form data.
     *
     * @return self New instance with the specified context.
     */
    final public function withContext(Context $context): self
    {
        $new = clone $this;
        $new->context = $context;
        return $new;
    }

    /**
     * Renders the filter widget using the current context.
     *
     * @return string The rendered HTML for the filter input.
     */
    final public function render(): string
    {
        return $this->renderFilter($this->context);
    }

    /**
     * Renders the specific filter input implementation.
     *
     * Implement this method to define how your specific filter widget should
     * render its input. Use the provided context to access:
     * - Property name ({@see Context::$property})
     * - Current value ({@see Context::$value})
     * - Form ID ({@see Context::$formId})
     *
     * @param Context $context The filter context to use for rendering.
     *
     * @return string The rendered HTML for the filter input.
     */
    abstract public function renderFilter(Context $context): string;
}
