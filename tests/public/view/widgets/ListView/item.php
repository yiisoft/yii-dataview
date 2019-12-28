<?php

/**
 * @var array $model
 * @var int $key
 * @var int $index
 * @var object $widget
 */

echo sprintf(
    'Item #%d: %s - Widget: %s',
    $index,
    $model['login'],
    get_class($widget)
);
