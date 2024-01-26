<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\YiiRouter;

use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\BaseListView;

/**
 * @psalm-import-type UrlArguments from BaseListView
 */
final class UrlCreator
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @psalm-param UrlArguments $arguments
     */
    public function __invoke(array $arguments, array $queryParameters): string
    {
        return $this->urlGenerator->generateFromCurrent($arguments, $queryParameters);
    }
}
