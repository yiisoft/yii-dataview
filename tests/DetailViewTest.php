<?php

namespace Yiisoft\Yii\DataView\Tests;

use Yiisoft\Arrays\ArrayableInterface;
use Yiisoft\Arrays\ArrayableTrait;
use Yiisoft\Yii\DataView\DetailView;

/**
 * @group widgets
 */
class DetailViewTest extends TestCase
{
    public function testAttributeValue(): void
    {
        $model = new ModelMock();
        $model->id = 123;

        $widget = PublicDetailView::widget()
            ->withModel($model)
            ->withTemplate('{label}:{value}')
            ->withAttributes(
                [
                    'id',
                    [
                        'attribute' => 'id',
                        'value' => 1,
                    ],
                    [
                        'attribute' => 'id',
                        'value' => '1',
                    ],
                    [
                        'attribute' => 'id',
                        'value' => $model->getDisplayedId(),
                    ],
                    [
                        'attribute' => 'id',
                        'value' => static function (ModelMock $model) {
                            return $model->getDisplayedId();
                        },
                    ],
                ]
            );

        $this->assertEquals('Id:123', $widget->renderAttr($widget->getAttributes()[0], 0));
        $this->assertEquals('Id:1', $widget->renderAttr($widget->getAttributes()[1], 1));
        $this->assertEquals('Id:1', $widget->renderAttr($widget->getAttributes()[2], 2));
        $this->assertEquals('Id:Displayed 123', $widget->renderAttr($widget->getAttributes()[3], 3));
        $this->assertEquals('Id:Displayed 123', $widget->renderAttr($widget->getAttributes()[4], 4));
        $this->assertEquals(2, $model->getDisplayedIdCallCount());
    }

    /**
     * @see https://github.com/yiisoft/yii2/issues/13243
     */
    public function testUnicodeAttributeNames(): void
    {
        $model = new UnicodeAttributesModelMock();
        $model->ИдентификаторТовара = 'A00001';
        $model->το_αναγνωριστικό_του = 'A00002';

        $widget = PublicDetailView::widget()
            ->withModel($model)
            ->withTemplate('{label}:{value}')
            ->withAttributes(
                [
                    'ИдентификаторТовара',
                    'το_αναγνωριστικό_του',
                ]
            );

        $this->assertEquals(
            'Идентификатор Товара:A00001',
            $widget->renderAttr($widget->getAttributes()[0], 0)
        );
        $this->assertEquals(
            'Το Αναγνωριστικό Του:A00002',
            $widget->renderAttr($widget->getAttributes()[1], 1)
        );
    }

    public function testAttributeVisible(): void
    {
        $model = new ModelMock();
        $model->id = 123;

        $widget = PublicDetailView::widget()
            ->withModel($model)
            ->withTemplate('{label}:{value}')
            ->withAttributes(
                [
                    [
                        'attribute' => 'id',
                        'value' => $model->getDisplayedId(),
                    ],
                    [
                        'attribute' => 'id',
                        'value' => $model->getDisplayedId(),
                        'visible' => false,
                    ],
                    [
                        'attribute' => 'id',
                        'value' => $model->getDisplayedId(),
                        'visible' => true,
                    ],
                    [
                        'attribute' => 'id',
                        'value' => static function (ModelMock $model) {
                            return $model->getDisplayedId();
                        },
                    ],
                    [
                        'attribute' => 'id',
                        'value' => static function (ModelMock $model) {
                            return $model->getDisplayedId();
                        },
                        'visible' => false,
                    ],
                    [
                        'attribute' => 'id',
                        'value' => static function (ModelMock $model) {
                            return $model->getDisplayedId();
                        },
                        'visible' => true,
                    ],
                ]
            );

        $this->assertEquals(
            [
                0 => [
                    'attribute' => 'id',
                    'format' => 'text',
                    'label' => 'Id',
                    'value' => 'Displayed 123',
                ],
                2 => [
                    'attribute' => 'id',
                    'format' => 'text',
                    'label' => 'Id',
                    'value' => 'Displayed 123',
                    'visible' => true,
                ],
                3 => [
                    'attribute' => 'id',
                    'format' => 'text',
                    'label' => 'Id',
                    'value' => 'Displayed 123',
                ],
                5 => [
                    'attribute' => 'id',
                    'format' => 'text',
                    'label' => 'Id',
                    'value' => 'Displayed 123',
                    'visible' => true,
                ],
            ],
            $widget->getAttributes()
        );
        $this->assertEquals(5, $model->getDisplayedIdCallCount());
    }

    public function testRelationAttribute(): void
    {
        $model = new ModelMock();
        $model->id = 123;
        $model->setRelated(new ModelMock());
        $model->getRelated()->id = 456;

        $widget = PublicDetailView::widget()
            ->withModel($model)
            ->withTemplate('{label}:{value}')
            ->withAttributes(
                [
                    'id',
                    '_related.id',
                ]
            );

        $this->assertEquals('Id:123', $widget->renderAttr($widget->getAttributes()[0], 0));
        $this->assertEquals(
            'Related Id:456',
            $widget->renderAttr($widget->getAttributes()[1], 1)
        );

        // test null relation
        $model->setRelated(null);

        $widget = PublicDetailView::widget()
            ->withModel($model)
            ->withTemplate('{label}:{value}')
            ->withAttributes(
                [
                    'id',
                    '_related.id',
                ]
            );

        $this->assertEquals('Id:123', $widget->renderAttr($widget->getAttributes()[0], 0));
        $this->markTestIncomplete('Needs to implement null-value');
        $this->assertEquals(
            'Related Id:<span class="not-set">(not set)</span>',
            $widget->renderAttr($widget->getAttributes()[1], 1)
        );
    }

    /**
     * @dataProvider modelsProvider()
     * @param array $expectedValue
     * @param $model
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     */
    public function testArrayModel(array $expectedValue, $model): void
    {
        $widget = PublicDetailView::widget()
            ->withModel(is_callable($model) ? $model() : $model)
            ->init();

        $this->assertEquals($expectedValue, $widget->getAttributes());
    }

    public function modelsProvider(): array
    {
        return [
            'Array Model' => [
                [
                    [
                        'attribute' => 'id',
                        'format' => 'text',
                        'label' => 'Id',
                        'value' => 1,
                    ],
                    [
                        'attribute' => 'text',
                        'format' => 'text',
                        'label' => 'Text',
                        'value' => 'I`m an array',
                    ],
                ],
                [
                    'id' => 1,
                    'text' => 'I`m an array',
                ],
            ],
            'Object Model' => [
                [
                    [
                        'attribute' => 'id',
                        'format' => 'text',
                        'label' => 'Id',
                        'value' => 1,
                    ],
                    [
                        'attribute' => 'text',
                        'format' => 'text',
                        'label' => 'Text',
                        'value' => 'I`m an object',
                    ],
                ],
                static function () {
                    $model = new ModelMock();
                    $model->id = 1;
                    $model->text = 'I`m an object';

                    return $model;
                },
            ],
            'Arrayble Model' => [
                [
                    [
                        'attribute' => 'id',
                        'format' => 'text',
                        'label' => 'Id',
                        'value' => 1,
                    ],
                    [
                        'attribute' => 'text',
                        'format' => 'text',
                        'label' => 'Text',
                        'value' => 'I`m arrayable',
                    ],
                ],
                static function () {
                    $model = new ArrayableInterfaceMock();
                    $model->id = 1;
                    $model->text = 'I`m arrayable';

                    return $model;
                },
            ],
        ];
    }

    public function testOptionsTags(): void
    {
        $expectedValue = '<tr><th tooltip="Tooltip">Text</th><td class="bg-red">I`m an array</td></tr>';

        $widget = PublicDetailView::widget()
            ->withModel(
                [
                    'text' => 'I`m an array',
                ]
            )
            ->withAttributes(
                [
                    [
                        'attribute' => 'text',
                        'label' => 'Text',
                        'contentOptions' => ['class' => 'bg-red'],
                        'captionOptions' => ['tooltip' => 'Tooltip'],
                    ],
                ]
            );

        foreach ($widget->getAttributes() as $index => $attribute) {
            $a = $widget->renderAttr($attribute, $index);
            $this->assertEquals($expectedValue, $a);
        }
    }

    /**
     * @see https://github.com/yiisoft/yii2/issues/15536
     */
    public function testShouldTriggerInitEvent(): void
    {
        $initTriggered = false;
        $model = new ModelMock();
        $model->id = 1;
        $model->text = 'I`m an object';

        $this->markTestIncomplete('Need to implement EventDispatcherListener');
        $widget = PublicDetailView::widget()
            ->withModel($model)
            ->on(
                'on widget.init',
                function () use (&$initTriggered) {
                    $initTriggered = true;
                },
                );

        $this->assertTrue($initTriggered);
    }
}

/**
 * Helper Class.
 */
class ArrayableInterfaceMock implements ArrayableInterface
{
    use ArrayableTrait;

    public int $id;

    public string $text;
}

/**
 * Helper Class.
 */
class ModelMock
{
    public ?int $id;
    public ?string $text;

    public ?object $_related;
    private int $_displayedIdCallCount = 0;

    public function getRelated()
    {
        return $this->_related;
    }

    public function setRelated($related): void
    {
        $this->_related = $related;
    }

    public function getDisplayedId(): string
    {
        $this->_displayedIdCallCount++;

        return "Displayed $this->id";
    }

    public function getDisplayedIdCallCount(): int
    {
        return $this->_displayedIdCallCount;
    }
}

/**
 * Used for testing attributes containing non-English characters.
 */
class UnicodeAttributesModelMock
{
    /**
     * Product's ID (Russian).
     *
     * @var mixed
     */
    public $ИдентификаторТовара;
    /**
     * ID (Greek).
     *
     * @var mixed
     */
    public $το_αναγνωριστικό_του;
}

class PublicDetailView extends DetailView
{
    public function renderAttr($attribute, $index): string
    {
        return $this->renderAttribute($attribute, $index);
    }
}
