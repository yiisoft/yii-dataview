<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\BaseListView;

/**
 * URL creator for list views that generates URLs based on the current route.
 *
 * @psalm-import-type UrlArguments from BaseListView
 */
final class UrlCreator
{
    /**
     * Creates a new URL creator instance.
     *
     * @param UrlGeneratorInterface $urlGenerator The URL generator service used
     * to generate URLs from the current route.
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * Generates a URL based on the current route with modified parameters.
     *
     * @param array $arguments Route arguments to override in the current route.
     * @psalm-param UrlArguments $arguments
     * @param array $queryParameters Query parameters to append to the URL.
     *
     * @return string The generated URL with the specified modifications.
     */
    public function __invoke(array $arguments, array $queryParameters): string
    {
        return $this->urlGenerator->generateFromCurrent($arguments, $queryParameters);
    }
}
