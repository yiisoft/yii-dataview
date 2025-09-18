<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use Yiisoft\Yii\DataView\Pagination\PaginationContext;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSetException;
use Yiisoft\Yii\DataView\Pagination\PaginatorNotSupportedException;
use Yiisoft\Yii\DataView\Tests\Support\Assert;
use Yiisoft\Yii\DataView\Tests\Support\TestTrait;

final class OffsetPaginationTest extends TestCase
{
    use TestTrait;

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    public function testRenderPaginatorEmptyData(): void
    {
        $offsetPaginator = $this->createOffsetPaginator([], 10);

        Assert::equalsWithoutLE(
            <<<HTML
            <div id="w1-grid">
            <table>
            <thead>
            <tr></tr>
            </thead>
            <tbody>
            <tr>
            <td colspan="0">No results found.</td>
            </tr>
            </tbody>
            </table>
            </div>
            HTML,
            GridView::widget()
                ->id('w1-grid')
                ->dataReader($offsetPaginator)
                ->paginationWidget(OffsetPagination::widget())
                ->render(),
        );
    }

    public function testNotSetPaginator(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(PaginatorNotSetException::class);
        $this->expectExceptionMessage('Failed to create widget because "paginator" is not set.');
        Assert::invokeMethod($pagination, 'getPaginator');
    }

    public function testThrowExceptionForUnsupportedPaginator(): void
    {
        $pagination = OffsetPagination::widget();
        $keysetPaginator = $this->createKeysetPaginator([], 10);

        $this->expectException(PaginatorNotSupportedException::class);
        $this->expectExceptionMessage(sprintf('Paginator "%s" is not supported.', $keysetPaginator::class));

        $pagination->withPaginator($keysetPaginator);
    }

    public function testThrowExceptionForContainerTagEmptyValue(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');

        $pagination->containerTag('');
    }

    public function testThrowExceptionForListTagEmptyValue(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');

        $pagination->listTag('');
    }

    public function testThrowExceptionForItemTagEmptyValue(): void
    {
        $pagination = OffsetPagination::widget();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');

        $pagination->itemTag('');
    }

    public function testRenderWithEmptyItems(): void
    {
        $result = OffsetPagination::create(
            $this->createOffsetPaginator([], 10),
            'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
            'http://example.com/',
        )
            ->render();

        $this->assertSame('', $result);
    }

    public function testAddLinkClass(): void
    {
        $offsetPagination = OffsetPagination::create(
            $this->createOffsetPaginator([], 10),
            'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
            'http://example.com/',
        )
            ->showOnSinglePage()
            ->addLinkClass('test-class');

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a class="test-class" href="http://example.com/">⟪</a>
            <a class="test-class" href="http://example.com/">⟨</a>
            <a class="test-class" href="http://example.com/">1</a>
            <a class="test-class" href="http://example.com/?page=0">⟩</a>
            <a class="test-class" href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a class="test-class test-class-1 test-class-2" href="http://example.com/">⟪</a>
            <a class="test-class test-class-1 test-class-2" href="http://example.com/">⟨</a>
            <a class="test-class test-class-1 test-class-2" href="http://example.com/">1</a>
            <a class="test-class test-class-1 test-class-2" href="http://example.com/?page=0">⟩</a>
            <a class="test-class test-class-1 test-class-2" href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->addLinkClass('test-class-1', null, 'test-class-2')->render(),
        );
    }

    public function testContainerTagWithAttributes(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <div class="test-class">
            <a href="http://example.com/">⟪</a>
            <a href="http://example.com/">⟨</a>
            <a href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">⟫</a>
            </div>
            HTML,
            OffsetPagination::create(
                $this->createOffsetPaginator([], 10),
                'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
                'http://example.com/',
            )
                ->showOnSinglePage()
                ->containerTag('div')
                ->containerAttributes(['class' => 'test-class'])
                ->render(),
        );
    }

    public function testCurrentLinkClass(): void
    {
        $offsetPagination = OffsetPagination::create(
            $this->createOffsetPaginator([], 10),
            'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
            'http://example.com/',
        )
            ->showOnSinglePage()
            ->currentLinkClass('test-class');

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="http://example.com/">⟪</a>
            <a href="http://example.com/">⟨</a>
            <a class="test-class" href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="http://example.com/">⟪</a>
            <a href="http://example.com/">⟨</a>
            <a class="test-class-1" href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->currentLinkClass('test-class-1')->render(),
        );
    }

    public function testDisabledLinkClass(): void
    {
        $offsetPagination = OffsetPagination::create(
            $this->createOffsetPaginator([], 10),
            'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
            'http://example.com/',
        )
            ->showOnSinglePage()
            ->disabledLinkClass('test-class');

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a class="test-class" href="http://example.com/">⟪</a>
            <a class="test-class" href="http://example.com/">⟨</a>
            <a href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a class="test-class-1" href="http://example.com/">⟪</a>
            <a class="test-class-1" href="http://example.com/">⟨</a>
            <a href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->disabledLinkClass('test-class-1')->render(),
        );
    }

    public function testLabelFirst(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="http://example.com/">First</a>
            <a href="http://example.com/">⟨</a>
            <a href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            OffsetPagination::create(
                $this->createOffsetPaginator([], 10),
                'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
                'http://example.com/',
            )
                ->showOnSinglePage()
                ->labelFirst('First')
                ->render(),
        );
    }

    public function testLabelLast(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="http://example.com/">⟪</a>
            <a href="http://example.com/">⟨</a>
            <a href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">Last</a>
            </nav>
            HTML,
            OffsetPagination::create(
                $this->createOffsetPaginator([], 10),
                'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
                'http://example.com/',
            )
                ->showOnSinglePage()
                ->labelLast('Last')
                ->render(),
        );
    }

    public function testLabelNext(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="http://example.com/">⟪</a>
            <a href="http://example.com/">⟨</a>
            <a href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">Next</a>
            <a href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            OffsetPagination::create(
                $this->createOffsetPaginator([], 10),
                'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
                'http://example.com/',
            )
                ->showOnSinglePage()
                ->labelNext('Next')
                ->render(),
        );
    }

    public function testLabelPrevious(): void
    {
        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a href="http://example.com/">⟪</a>
            <a href="http://example.com/">Previous</a>
            <a href="http://example.com/">1</a>
            <a href="http://example.com/?page=0">⟩</a>
            <a href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            OffsetPagination::create(
                $this->createOffsetPaginator([], 10),
                'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
                'http://example.com/',
            )
                ->showOnSinglePage()
                ->labelPrevious('Previous')
                ->render(),
        );
    }

    public function testLinkClass(): void
    {
        $offsetPagination = OffsetPagination::create(
            $this->createOffsetPaginator([], 10),
            'http://example.com/?page=' . PaginationContext::URL_PLACEHOLDER,
            'http://example.com/',
        )
            ->showOnSinglePage()
            ->LinkClass('test-class');

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a class="test-class" href="http://example.com/">⟪</a>
            <a class="test-class" href="http://example.com/">⟨</a>
            <a class="test-class" href="http://example.com/">1</a>
            <a class="test-class" href="http://example.com/?page=0">⟩</a>
            <a class="test-class" href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->render(),
        );

        Assert::equalsWithoutLE(
            <<<HTML
            <nav>
            <a class="test-class-1" href="http://example.com/">⟪</a>
            <a class="test-class-1" href="http://example.com/">⟨</a>
            <a class="test-class-1" href="http://example.com/">1</a>
            <a class="test-class-1" href="http://example.com/?page=0">⟩</a>
            <a class="test-class-1" href="http://example.com/?page=0">⟫</a>
            </nav>
            HTML,
            $offsetPagination->linkClass('test-class-1')->render(),
        );
    }
}
