<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Html\Html;

use function array_key_exists;

/**
 * @internal Helper for working with HTML.
 */
final class HtmlHelper
{
    /**
     * Merges HTML attributes on top of a base set, appending (rather than replacing) the `class` attribute.
     */
    public static function mergeAttributes(array $attributes, array $overlay): array
    {
        if (array_key_exists('class', $overlay)) {
            $class = $overlay['class'];
            unset($overlay['class']);
            /** @psalm-suppress MixedArgument, MixedArgumentTypeCoercion */
            Html::addCssClass($attributes, $class);
        }

        return array_merge($attributes, $overlay);
    }
}
