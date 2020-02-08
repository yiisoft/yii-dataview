<?php

namespace Yiisoft\Yii\DataView;

class MessageFormatter
{
    /**
     * In future it will deleted and used any translator instead this helper
     * Copied from https://github.com/yiisoft/validator/blob/master/src/Rule.php#L38
     * @param string $message
     * @param array $arguments
     * @return string
     */
    public static function formatMessage(string $message, array $arguments = []): string
    {
        $replacements = [];
        foreach ($arguments as $key => $value) {
            if (\is_array($value)) {
                $value = 'array';
            } elseif (\is_object($value)) {
                $value = 'object';
            } elseif (\is_resource($value)) {
                $value = 'resource';
            }
            $replacements['{' . $key . '}'] = $value;
        }
        // TODO: move it to upper level and make it configurable?
        return strtr($message, $replacements);
    }
}
