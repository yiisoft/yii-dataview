<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Pagination;

use Stringable;
use Yiisoft\Data\Paginator\PageToken;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\BaseListView;

/**
 * Context class for pagination widgets that provides URL generation and configuration.
 */
final class PaginationContext
{
    /**
     * Placeholder used in URL patterns that will be replaced with the actual page token.
     */
    public const URL_PLACEHOLDER = 'YII-DATAVIEW-PAGE-PLACEHOLDER';

    /**
     * @param string $nextUrlPattern URL pattern for next page links. Must contain {@see URL_PLACEHOLDER}.
     * @param string $previousUrlPattern URL pattern for previous page links. Must contain {@see URL_PLACEHOLDER}.
     * @param string $firstPageUrl URL used on the first page.
     * @param bool $accessibility Whether pagination widgets should add accessibility attributes `aria-*`
     * automatically.
     * @param TranslatorInterface|null $translator Translator used for pagination messages.
     * @param string $translationCategory Category used with the translator.
     */
    public function __construct(
        public readonly string $nextUrlPattern,
        public readonly string $previousUrlPattern,
        public readonly string $firstPageUrl,
        public readonly bool $accessibility = false,
        private readonly ?TranslatorInterface $translator = null,
        private readonly string $translationCategory = BaseListView::DEFAULT_TRANSLATION_CATEGORY,
    ) {}

    /**
     * Translate a message using the pagination translation category.
     *
     * @param string|Stringable $id Message ID to translate.
     *
     * @return string Translated message, or the message ID unchanged when no translator is set.
     */
    public function translate(string|Stringable $id): string
    {
        return $this->translator?->translate($id, category: $this->translationCategory) ?? (string) $id;
    }

    /**
     * Creates a URL for the given page token.
     *
     * This method replaces the URL_PLACEHOLDER in either the next or previous
     * URL pattern (depending on the token type) with the URL-encoded token value.
     *
     * @param PageToken $pageToken Token for the page.
     *
     * @return string The generated URL with the token value properly encoded.
     */
    public function createUrl(PageToken $pageToken): string
    {
        return str_replace(
            self::URL_PLACEHOLDER,
            urlencode($pageToken->value),
            $pageToken->isPrevious ? $this->previousUrlPattern : $this->nextUrlPattern,
        );
    }
}
