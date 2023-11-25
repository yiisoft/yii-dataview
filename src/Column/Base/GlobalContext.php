<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Stringable;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\Translator\TranslatorInterface;

final class GlobalContext
{
    public function __construct(
        public readonly ReadableDataInterface $dataReader,
        public readonly array $sortLinkAttributes,
        public readonly array $urlArguments,
        public readonly array $urlQueryParameters,
        public readonly ?string $filterModelName,
        private readonly TranslatorInterface $translator,
        private readonly string $translationCategory,
    ) {
    }

    public function translate(string|Stringable $id): string
    {
        return $this->translator->translate($id, category: $this->translationCategory);
    }
}
