<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\ListView;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\Exception;
use Yiisoft\Yii\DataView\ListView;
use Yiisoft\Yii\DataView\Tests\Support\Mock;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class ExceptionTest extends TestCase
{
    use TestTrait;

    private array $data = [
        ['id' => 1, 'name' => 'John', 'age' => 20],
        ['id' => 2, 'name' => 'Mary', 'age' => 21],
    ];

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
        ListView::widget()
            ->itemView('//_listview')
            ->translator(Mock::translator('en'))
            ->render();
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
        ListView::widget()
            ->itemView('//_listview')
            ->paginator($this->createOffsetPaginator($this->data, 10))
            ->webView(Mock::webView())
            ->render();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testGetWebView(): void
    {
        $this->expectException(Exception\WebViewNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "webview" is not set.');
        ListView::widget()
            ->itemView('//_listview')
            ->paginator($this->createOffsetPaginator($this->data, 10))
            ->translator(Mock::translator('en'))
            ->render();
    }

    public function testItemViewWithNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The "itemView" property must be set.');
        ListView::widget()
            ->paginator($this->createOffsetPaginator($this->data, 10))
            ->translator(Mock::translator('en'))
            ->render();
    }
}
