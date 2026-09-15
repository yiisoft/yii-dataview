<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;

use function extension_loaded;

/**
 * Creates the translator used by widgets when none is passed explicitly.
 *
 * @internal
 */
final class DefaultTranslatorFactory
{
    /**
     * @param string $category A name for {@see CategorySource} to register in the translator.
     *
     * @return TranslatorInterface Translator instance used for translations of messages.
     */
    public static function create(string $category): TranslatorInterface
    {
        $categorySource = new CategorySource(
            $category,
            new IdMessageReader(),
            extension_loaded('intl') ? new IntlMessageFormatter() : new SimpleMessageFormatter(),
        );
        $translator = new Translator();
        $translator->addCategorySources($categorySource);
        return $translator;
    }
}
