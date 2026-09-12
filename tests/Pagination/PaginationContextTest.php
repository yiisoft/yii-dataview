<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\Pagination;

use PHPUnit\Framework\TestCase;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\InMemoryMessageSource;
use Yiisoft\Translator\Translator;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\Pagination\PaginationContext;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;

final class PaginationContextTest extends TestCase
{
    public function testTranslateWithoutTranslatorReturnsIdUnchanged(): void
    {
        $context = new PaginationContext('/next', '/previous', '/');

        $this->assertSame('Next page', $context->translate('Next page'));
        $this->assertSame('Next page', $context->translate(new StringableObject('Next page')));
    }

    public function testTranslateWithoutTranslatorSubstitutesParameters(): void
    {
        $context = new PaginationContext('/next', '/previous', '/');

        $this->assertSame('Page 2', $context->translate('Page {page}', ['page' => '2']));
    }

    public function testTranslateUsesTranslatorWithConfiguredCategory(): void
    {
        $messageSource = new InMemoryMessageSource();
        $messageSource->write('pager', 'en', ['Next page' => 'Nächste Seite']);
        $translator = (new Translator('en'))->addCategorySources(
            new CategorySource('pager', $messageSource),
        );

        $context = new PaginationContext('/next', '/previous', '/', true, $translator, 'pager');

        $this->assertSame('Nächste Seite', $context->translate('Next page'));
    }

    public function testTranslateDefaultCategory(): void
    {
        $messageSource = new InMemoryMessageSource();
        $messageSource->write(
            BaseListView::DEFAULT_TRANSLATION_CATEGORY,
            'en',
            ['Next page' => 'Nächste Seite'],
        );
        $translator = (new Translator('en'))->addCategorySources(
            new CategorySource(BaseListView::DEFAULT_TRANSLATION_CATEGORY, $messageSource),
        );

        $context = new PaginationContext('/next', '/previous', '/', true, $translator);

        $this->assertSame('Nächste Seite', $context->translate('Next page'));
    }
}
