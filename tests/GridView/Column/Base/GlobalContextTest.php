<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests\GridView\Column\Base;

use PHPUnit\Framework\TestCase;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\InMemoryMessageSource;
use Yiisoft\Translator\Translator;
use Yiisoft\Yii\DataView\BaseListView;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;
use Yiisoft\Yii\DataView\Tests\Support\TestHelper;

/**
 * @covers \Yiisoft\Yii\DataView\GridView\Column\Base\GlobalContext
 * @covers \Yiisoft\Yii\DataView\GridView\Column\Base\Cell
 * @covers \Yiisoft\Yii\DataView\Url\UrlConfig
 * @covers \Yiisoft\Yii\DataView\Url\UrlParametersFactory
 */
final class GlobalContextTest extends TestCase
{
    public function testTranslate(): void
    {
        $messageSource = new InMemoryMessageSource();
        $messageSource->write(
            BaseListView::DEFAULT_TRANSLATION_CATEGORY,
            'en',
            ['test.message' => 'Translated Message'],
        );
        $translator = (new Translator('en'))
            ->addCategorySources(
                new CategorySource(BaseListView::DEFAULT_TRANSLATION_CATEGORY, $messageSource)
            );
        $context = TestHelper::createGlobalContext(translator: $translator);

        $this->assertSame('Translated Message', $context->translate('test.message'));
        $this->assertSame('Translated Message', $context->translate(new StringableObject('test.message')));
    }
}
