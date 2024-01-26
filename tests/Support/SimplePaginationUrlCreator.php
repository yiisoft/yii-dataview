<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

final class SimplePaginationUrlCreator
{
    public function __invoke(array $arguments, array $queryParameters): string
    {
        $url = '/route';

        foreach ($arguments as $name => $value) {
            $url .= '/' . $name . '-' . $value;
        }

        if (!empty($queryParameters)) {
            $url .= '?' . http_build_query($queryParameters);
        }

        return $url;
    }
}
