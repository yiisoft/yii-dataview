<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Exception;

use RuntimeException;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

/**
 * Exception thrown when attempting to create a widget that requires URL generation without a URL generator.
 *
 * This exception is thrown in scenarios where a widget (such as GridView or ListView) needs to generate
 * URLs (e.g., for sorting, pagination, or filtering) but no URL generator is configured. The URL generator
 * is essential for creating proper URLs that maintain the widget's state and functionality.
 */
final class UrlGeneratorNotSetException extends RuntimeException implements FriendlyExceptionInterface
{
    /**
     * Creates a new UrlGeneratorNotSetException instance.
     *
     * @param string $message Custom error message. If empty, uses the default message from getName().
     */
    public function __construct(string $message = '')
    {
        $message = $message === '' ? $this->getName() : $message;
        parent::__construct($message);
    }

    /**
     * Gets the default error message.
     *
     * @return string The error message indicating that the URL generator is not set.
     */
    public function getName(): string
    {
        return 'Failed to create widget because "urlgenerator" is not set.';
    }

    /**
     * Gets the solution message with instructions for fixing the issue.
     *
     * The solution includes:
     * - How to configure the URL generator
     * - The required interface to implement
     * - Link to router documentation for more details
     *
     * @return string|null The solution message in markdown format.
     */
    public function getSolution(): ?string
    {
        return <<<SOLUTION
            You can configure the `urlGenerator` property in the widget constructor. Use
            `UrlGeneratorInterface::class`.
            For more information [see the router documentation](https://github.com/yiisoft/router).
        SOLUTION;
    }
}
