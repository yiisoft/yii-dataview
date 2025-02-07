<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Translator\TranslatorInterface;

/**
 * GlobalContext provides shared context for all columns in a grid:
 *
 * - Data reading and iteration
 * - URL generation and parameters
 * - Translation services
 */
final class GlobalContext
{
    /**
     * @psalm-param array<string,scalar|Stringable|null> $urlArguments
     * @param ReadableDataInterface $dataReader Data reader for accessing grid data.
     * @param array<string,scalar|Stringable|null> $urlArguments URL path arguments for link generation.
     * @param array $urlQueryParameters URL query parameters for link generation.
     * @param TranslatorInterface $translator Translator service for internationalizing grid content.
     * @param string $translationCategory Category used for translations within the grid.
     */
    public function __construct(
        public readonly ReadableDataInterface $dataReader,
        public readonly array $urlArguments,
        public readonly array $urlQueryParameters,
        private readonly TranslatorInterface $translator,
        private readonly string $translationCategory,
    ) {
    }

    /**
     * Translate a message using the grid's translation category.
     *
     * @param string|Stringable $id Message ID to translate.
     *
     * @return string Translated message.
     */
    public function translate(string|Stringable $id): string
    {
        return $this->translator->translate($id, category: $this->translationCategory);
    }
}
