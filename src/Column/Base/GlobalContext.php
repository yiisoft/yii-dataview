<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Translator\TranslatorInterface;

/**
 * GlobalContext provides shared context for all columns in a grid.
 *
 * This immutable class serves as a centralized container for resources and services
 * that are shared across all columns in a grid. It provides access to:
 *
 * Key components:
 * - Data reading and iteration capabilities
 * - URL generation parameters and arguments
 * - Translation services and configuration
 *
 * Common use cases:
 * - Accessing grid data
 * - Generating column links with consistent URL structure
 * - Internationalizing column headers and content
 * - Maintaining consistent URL parameters across columns
 *
 * Example usage:
 * ```php
 * class CustomColumnRenderer implements ColumnRendererInterface
 * {
 *     public function renderBody(ColumnInterface $column, Cell $cell, DataContext $context): Cell
 *     {
 *         // Access shared URL parameters
 *         $urlParams = array_merge(
 *             $context->globalContext->pathArguments,
 *             ['id' => $context->key]
 *         );
 *
 *         // Translate column content
 *         $label = $context->globalContext->translate('view.details');
 *
 *         // Create link with consistent URL structure
 *         $url = Url::to('/view', $urlParams);
 *
 *         return $cell->content(Html::a($label, $url));
 *     }
 * }
 * ```
 */
final class GlobalContext
{
    /**
     * Creates a new global context instance.
     *
     * @psalm-param array<string,scalar|Stringable|null> $pathArguments
     *
     * @param ReadableDataInterface $dataReader Data reader for accessing grid data.
     *                                         This provides a unified interface for reading
     *                                         data from various sources (arrays, databases, etc.).
     *
     * @param array<string,scalar|Stringable|null> $pathArguments URL path arguments for link generation.
     *                                                          These are used to maintain consistent
     *                                                          URL structure across all grid links.
     *
     * @param array $queryParameters URL query parameters for link generation.
     *                                These parameters are appended to URLs as query string.
     *                                Useful for maintaining state (sorting, filtering, etc.).
     *
     * @param TranslatorInterface $translator Translator service for internationalizing grid content.
     *                                      Used to translate column headers, labels, and other text.
     *
     * @param string $translationCategory Category used for translations within the grid.
     *                                  This helps organize translations and provides context
     *                                  for translators.
     */
    public function __construct(
        public readonly ReadableDataInterface $dataReader,
        public readonly array $pathArguments,
        public readonly array $queryParameters,
        private readonly TranslatorInterface $translator,
        private readonly string $translationCategory,
    ) {
    }

    /**
     * Translate a message using the grid's translation category.
     *
     * This method provides a convenient way to translate text within the grid's context.
     * It automatically uses the translation category specified in the constructor.
     *
     * Example:
     * ```php
     * // Translate a simple message
     * $label = $context->translate('grid.view');
     *
     * // Translate with parameters
     * $message = new MessageContext('grid.items.count')
     *     ->withParameter('count', $total);
     * $label = $context->translate($message);
     * ```
     *
     * @param string|Stringable $id Message ID to translate. This can be a simple string
     *                             or a MessageContext object with parameters.
     *
     * @return string Translated message in the current language.
     */
    public function translate(string|Stringable $id): string
    {
        return $this->translator->translate($id, category: $this->translationCategory);
    }
}
