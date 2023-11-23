<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Column\Base;

use Yiisoft\Data\Reader\ReadableDataInterface;

final class GlobalContext
{
    public function __construct(
        private ReadableDataInterface $dataReader,
        private array $sortLinkAttributes,
        private array $urlArguments = [],
        private array $urlQueryParameters = [],
        private ?string $filterModelName = null,
    ) {
    }

    public function getDataReader(): ReadableDataInterface
    {
        return $this->dataReader;
    }

    public function getSortLinkAttributes(): array
    {
        return $this->sortLinkAttributes;
    }

    public function getUrlArguments(): array
    {
        return $this->urlArguments;
    }

    public function getUrlQueryParameters(): array
    {
        return $this->urlQueryParameters;
    }

    public function getFilterModelName(): ?string
    {
        return $this->filterModelName;
    }
}
