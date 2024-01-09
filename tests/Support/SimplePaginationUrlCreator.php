<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use Yiisoft\Yii\DataView\PageContext;
use Yiisoft\Yii\DataView\UrlParameterPlace;

final class SimplePaginationUrlCreator
{
    public function __invoke(PageContext $context): string
    {
        $url = '/route';

        $arguments = [];
        if ($context->pageParameterPlace === UrlParameterPlace::PATH) {
            $url .= '/' . $context->pageName . '/' . $context->page;
        }
        if ($context->pageSizeParameterPlace == UrlParameterPlace::PATH) {
            $url .= '/' . $context->pageSizeParameterName . '/' . $context->pageSize;
        }

        $queryParameters = [];
        if ($context->pageParameterPlace === UrlParameterPlace::QUERY) {
            $queryParameters[$context->pageParameterName] = $context->page;
        }
        if ($context->pageSizeParameterPlace == UrlParameterPlace::QUERY) {
            $queryParameters[$context->pageSizeParameterName] = $context->pageSize;
        }
        $queryParameters = array_merge($queryParameters, $context->queryParameters);
        if (!empty($queryParameters)) {
            $url .= '?' . http_build_query($queryParameters);
        }

        return $url;
    }
}
