<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Translator\TranslatorInterface;

/**
 * GlobalContext provides shared context for all columns in a grid.
 */
final class GlobalContext
{
    /**
     * Creates a new global context instance.
     *
     * @param ReadableDataInterface $dataReader Data reader for accessing grid data.
     * @param array<string,scalar|Stringable|null> $pathArguments URL path arguments for link generation.
     * @psalm-param array<string,scalar|Stringable|null> $pathArguments
     * @param array $queryParameters URL query parameters for link generation.
     * @param TranslatorInterface $translator Translator service for internationalizing grid content.
     * @param string $translationCategory Category used for translations within the grid.
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
     * @param string|Stringable $id Message ID to translate.
     *
     * @return string Translated message in the current language.
     */
    public function translate(string|Stringable $id): string
    {
        return $this->translator->translate($id, category: $this->translationCategory);
    }
}
