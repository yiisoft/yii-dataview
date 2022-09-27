<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Support;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

use function str_replace;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class Assert extends TestCase
{
    /**
     * Asserting two strings equality ignoring line endings.
     *
     * @param string $expected The expected string.
     * @param string $actual The actual string.
     * @param string $message The message to display if the assertion fails.
     */
    public static function equalsWithoutLE(string $expected, string $actual, string $message = ''): void
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);

        self::assertEquals($expected, $actual, $message);
    }

    /**
     * Gets an inaccessible object property.
     *
     * @param object $object The object to get the property from.
     * @param string $propertyName The name of the property to get.
     */
    public static function inaccessibleProperty(object $object, string $propertyName): mixed
    {
        $class = new ReflectionClass($object);

        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        /** @var mixed $result */
        return $property->getValue($object);
    }

    /**
     * Invokes an inaccessible method.
     *
     * @param object $object The object to invoke the method on.
     * @param string $method The name of the method to invoke.
     * @param array $args The arguments to pass to the method.
     *
     * @throws ReflectionException
     */
    public static function invokeMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new ReflectionObject($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
