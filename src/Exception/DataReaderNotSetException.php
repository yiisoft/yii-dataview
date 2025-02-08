<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Exception;

use LogicException;

/**
 * Exception thrown when attempting to create a data view widget without setting a data reader.
 *
 * This exception is thrown in scenarios where a data view widget (such as `GridView` or `ListView`)
 * is instantiated without providing a required data reader component. The data reader is essential
 * for the widget to access and iterate over the data that needs to be displayed.
 */
final class DataReaderNotSetException extends LogicException
{
    /**
     * Creates a new `DataReaderNotSetException` instance.
     *
     * The exception message indicates that widget creation failed due to
     * a missing data reader component.
     */
    public function __construct()
    {
        parent::__construct('Failed to create widget because "dataReader" is not set.');
    }
}
