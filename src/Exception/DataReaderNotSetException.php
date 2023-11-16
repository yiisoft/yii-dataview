<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Exception;

use LogicException;

final class DataReaderNotSetException extends LogicException
{
    public function __construct()
    {
        parent::__construct('Failed to create widget because "dataReader" is not set.');
    }
}
