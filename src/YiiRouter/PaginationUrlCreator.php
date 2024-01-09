<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\PageContext;
use Yiisoft\Yii\DataView\UrlParameterPlace;

final class PaginationUrlCreator
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(PageContext $context): string
    {
        $arguments = [];
        if ($context->pageParameterPlace === UrlParameterPlace::PATH) {
            $arguments[$context->pageParameterName] = $context->page;
        }
        if ($context->pageSizeParameterPlace == UrlParameterPlace::PATH) {
            $arguments[$context->pageSizeParameterName] = $context->pageSize;
        }

        $queryParameters = [];
        if ($context->pageParameterPlace === UrlParameterPlace::QUERY) {
            $queryParameters[$context->pageParameterName] = $context->page;
        }
        if ($context->pageSizeParameterPlace == UrlParameterPlace::QUERY) {
            $queryParameters[$context->pageSizeParameterName] = $context->pageSize;
        }

        $this->urlGenerator->generateFromCurrent($arguments, $queryParameters);
    }
}
