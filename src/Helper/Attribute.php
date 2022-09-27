<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Helper;

use InvalidArgumentException;

final class Attribute
{
    /**
     * Generates an appropriate input name for the specified attribute name or expression.
     *
     * This method generates a name that can be used as the input name to collect user input for the specified
     * attribute. The name is generated according to the of the form and the given attribute name. For example, if the
     * form name of the `Post` form is `Post`, then the input name generated for the `content` attribute would be
     * `Post[content]`.
     *
     * @param string $formName The form name.
     * @param string $attribute The attribute name or expression.
     *
     * @throws InvalidArgumentException If the attribute name contains non-word characters or empty form name for
     * tabular inputs
     */
    public static function getInputName(string $formName, string $attribute): string
    {
        $data = self::parseAttribute($attribute);

        if ($formName === '' && $data['prefix'] === '') {
            return $attribute;
        }

        if ($formName !== '') {
            return $formName . $data['prefix'] . '[' . $data['name'] . ']' . $data['suffix'];
        }

        throw new InvalidArgumentException('The form name cannot be empty for tabular inputs.');
    }

    /**
     * This method parses an attribute expression and returns an associative array containing real attribute name,
     * prefix and suffix.
     *
     * For example: `['name' => 'content', 'prefix' => '', 'suffix' => '[0]']`
     *
     * An attribute expression is an attribute name prefixed and/or suffixed with array indexes. It is mainly used in
     * tabular data input and/or input of array type. Below are some examples:
     *
     * - `[0]content` is used in tabular data input to represent the "content" attribute for the first model in tabular
     *    input;
     * - `dates[0]` represents the first array element of the "dates" attribute;
     * - `[0]dates[0]` represents the first array element of the "dates" attribute for the first model in tabular
     *    input.
     *
     * @param string $attribute The attribute name or expression
     *
     * @throws InvalidArgumentException If the attribute name contains non-word characters.
     *
     *
     * @psalm-return string[]
     */
    private static function parseAttribute(string $attribute): array
    {
        if (!preg_match('/(^|.*\])([\w\.\+\-_]+)(\[.*|$)/u', $attribute, $matches)) {
            throw new InvalidArgumentException('Attribute name must contain word characters only.');
        }

        return ['name' => $matches[2], 'prefix' => $matches[1], 'suffix' => $matches[3]];
    }
}
