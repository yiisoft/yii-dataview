<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView;

use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView;
use Yiisoft\Yii\DataView\Exception;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ExceptionTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testGetPaginator(): void
    {
        $this->expectException(Exception\PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
        DataView\GridView::widget()->getPaginator();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testGetTranslator(): void
    {
        $this->expectException(Exception\TranslatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "translator" is not set.');
        DataView\GridView::widget()->getTranslator();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testGetUrlGenerator(): void
    {
        $this->expectException(Exception\UrlGeneratorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "urlgenerator" is not set.');
        DataView\GridView::widget()->getUrlGenerator();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testPaginator(): void
    {
        $this->expectException(Exception\PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
        DataView\GridView::widget()->render();
    }
}
