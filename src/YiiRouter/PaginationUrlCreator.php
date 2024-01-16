<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\PageContext;
use Yiisoft\Yii\DataView\UrlParameterType;

final class PaginationUrlCreator
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(PageContext $context): string
    {
        $arguments = [];
        if ($context->pageParameterType === UrlParameterType::PATH) {
            $arguments[$context->pageParameterName] = $context->page;
        }
        if ($context->pageSizeParameterType == UrlParameterType::PATH) {
            $arguments[$context->pageSizeParameterName] = $context->pageSize;
        }

        $queryParameters = $context->queryParameters;
        if ($context->pageParameterType === UrlParameterType::QUERY) {
            $queryParameters[$context->pageParameterName] = $context->isFirstPage || $context->isPreviousPage
                ? null
                : $context->page;
        }
        if ($context->previousPageParameterType === UrlParameterType::QUERY) {
            $queryParameters[$context->previousPageParameterName] = $context->isPreviousPage && !$context->isFirstPage
                ? $context->page
                : null;
        }
        if ($context->pageSizeParameterType == UrlParameterType::QUERY) {
            $queryParameters[$context->pageSizeParameterName] = $context->pageSize === $context->defaultPageSize
                ? null
                : $context->pageSize;
        }

        return $this->urlGenerator->generateFromCurrent($arguments, $queryParameters);
    }
}
