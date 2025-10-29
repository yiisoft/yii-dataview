<?php

declare(strict_types=1);

namespace DetailView;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Html\NoEncode;
use Yiisoft\Yii\DataView\DetailView\DataField;
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Yii\DataView\DetailView\FieldContext;
use Yiisoft\Yii\DataView\DetailView\GetValueContext;
use Yiisoft\Yii\DataView\DetailView\LabelContext;
use Yiisoft\Yii\DataView\DetailView\ValueContext;
use Yiisoft\Yii\DataView\Tests\Support\StringableObject;
use Yiisoft\Yii\DataView\ValuePresenter\SimpleValuePresenter;

final class DetailViewTest extends TestCase
{
    public function testBase(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(value: '-'),
                new DataField(property: 'name', label: 'Full Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt></dt>
            <dd>-</dd>
            <dt>Full Name</dt>
            <dd>John</dd>
            <dt>Age</dt>
            <dd>30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testFieldVisibility(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30, 'email' => 'john@example.com'])
            ->fields(
                new DataField(property: 'name', label: 'Full Name'),
                new DataField(property: 'age', label: 'Age', visible: false),
                new DataField(property: 'email', label: 'Email'),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Full Name</dt>
            <dd>John</dd>
            <dt>Email</dt>
            <dd>john@example.com</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithoutFields(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->render();

        $this->assertSame('', $html);
    }

    public function testWithContainerAndPrependAndAppend(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John'])
            ->fields(new DataField(property: 'name', label: 'Name'))
            ->containerTag('div')
            ->containerAttributes(['id' => 'TEST'])
            ->prepend('<div>Before</div>')
            ->append('<div>After</div>')
            ->render();

        $this->assertSame(
            <<<HTML
            <div id="TEST">
            <div>Before</div>
            <dl>
            <dt>Name</dt>
            <dd>John</dd>
            </dl>
            <div>After</div>
            </div>
            HTML,
            $html,
        );
    }

    public function testWithoutListTag(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John'])
            ->fields(new DataField(property: 'name', label: 'Name'))
            ->listTag(null)
            ->labelTag('i')
            ->valueTag('b')
            ->render();

        $this->assertSame(
            <<<HTML
            <i>Name</i>
            <b>John</b>
            HTML,
            $html,
        );
    }

    public function testWithoutLabelTag(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->labelTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            Name
            <dd>John</dd>
            Age
            <dd>30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithFieldPrependAndAppend(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John'])
            ->fields(new DataField(property: 'name', label: 'Name'))
            ->listTag(null)
            ->fieldTag('div')
            ->fieldPrepend('<span>START</span>')
            ->fieldAppend('<span>END</span>')
            ->render();

        $this->assertSame(
            <<<HTML
            <div>
            <span>START</span><dt>Name</dt>
            <dd>John</dd><span>END</span>
            </div>
            HTML,
            $html,
        );
    }

    public function testWithFieldAttributesArray(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->fieldTag('div')
            ->fieldAttributes(['class' => 'fw-bold'])
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <div class="fw-bold">
            <dt>Name</dt>
            <dd>John</dd>
            </div>
            <div class="fw-bold">
            <dt>Age</dt>
            <dd>30</dd>
            </div>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithFieldAttributesCallable(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(
                    property: 'name',
                    label: 'Name',
                    fieldAttributes: static fn(FieldContext $context) => [
                        'data-label' => 'THE ' . $context->field->property,
                    ],
                ),
                new DataField(
                    property: 'age',
                    label: 'Age',
                    fieldAttributes: ['data-label' => 'THE AGE'],
                ),
            )
            ->fieldTag('div')
            ->fieldAttributes(
                static fn(FieldContext $context) => ['class' => 'field-' . $context->field->property],
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <div class="field-name" data-label="THE name">
            <dt>Name</dt>
            <dd>John</dd>
            </div>
            <div class="field-age" data-label="THE AGE">
            <dt>Age</dt>
            <dd>30</dd>
            </div>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithLabelPrependAndAppend(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John'])
            ->fields(new DataField(property: 'name', label: 'Name'))
            ->labelPrepend('[')
            ->labelAppend(']')
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>[Name]</dt>
            <dd>John</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithLabelAttributesArray(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->labelAttributes(['class' => 'fw-bold'])
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt class="fw-bold">Name</dt>
            <dd>John</dd>
            <dt class="fw-bold">Age</dt>
            <dd>30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithLabelAttributesCallable(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(
                    property: 'name',
                    label: 'Name',
                    labelAttributes: static fn(LabelContext $context) => [
                        'data-label' => 'THE ' . $context->field->property,
                    ],
                ),
                new DataField(
                    property: 'age',
                    label: 'Age',
                    labelAttributes: ['data-label' => 'THE AGE'],
                ),
            )
            ->labelAttributes(
                static fn(LabelContext $context) => ['class' => 'label-' . $context->field->property],
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt class="label-name" data-label="THE name">Name</dt>
            <dd>John</dd>
            <dt class="label-age" data-label="THE AGE">Age</dt>
            <dd>30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithListAttributes(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->listAttributes(['class' => 'detail-list', 'data-test' => 'true'])
            ->render();

        $this->assertSame(
            <<<HTML
            <dl class="detail-list" data-test="true">
            <dt>Name</dt>
            <dd>John</dd>
            <dt>Age</dt>
            <dd>30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithCustomFieldTemplate(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->fieldTemplate('{value}<br>{label}')
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dd>John</dd><br><dt>Name</dt>
            <dd>30</dd><br><dt>Age</dt>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithValueAttributesArray(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->valueAttributes(['class' => 'fw-bold'])
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Name</dt>
            <dd class="fw-bold">John</dd>
            <dt>Age</dt>
            <dd class="fw-bold">30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithValueAttributesCallable(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(
                    property: 'name',
                    label: 'Name',
                    valueAttributes: static fn(ValueContext $context) => [
                        'data-label' => 'THE ' . $context->field->property,
                    ],
                ),
                new DataField(
                    property: 'age',
                    label: 'Age',
                    valueAttributes: ['data-label' => 'THE AGE'],
                ),
            )
            ->valueAttributes(
                static fn(ValueContext $context) => ['class' => 'value-' . $context->field->property],
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Name</dt>
            <dd class="value-name" data-label="THE name">John</dd>
            <dt>Age</dt>
            <dd class="value-age" data-label="THE AGE">30</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithValuePrependAndAppend(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->valuePrepend('(')
            ->valueAppend(')')
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Name</dt>
            <dd>(John)</dd>
            <dt>Age</dt>
            <dd>(30)</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithCustomValuePresenter(): void
    {
        $presenter = new SimpleValuePresenter(
            null: '(empty)',
            true: 'Yes',
            false: 'No',
        );

        $html = DetailView::widget()
            ->data(['name' => 'John', 'active' => true, 'notes' => null])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'active', label: 'Active'),
                new DataField(property: 'notes', label: 'Notes'),
            )
            ->valuePresenter($presenter)
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Name</dt>
            <dd>John</dd>
            <dt>Active</dt>
            <dd>Yes</dd>
            <dt>Notes</dt>
            <dd>(empty)</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithCallableFieldValue(): void
    {
        $html = DetailView::widget()
            ->data(['firstName' => 'John', 'lastName' => 'Doe', 'age' => 30])
            ->fields(
                new DataField(
                    label: 'Full Name',
                    value: static fn(GetValueContext $context) =>
                        $context->data['firstName'] . ' ' . $context->data['lastName']
                ),
                new DataField(
                    label: 'Status',
                    value: static fn(GetValueContext $context) =>
                        $context->data['age'] >= 18 ? 'Adult' : 'Minor'
                ),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Full Name</dt>
            <dd>John Doe</dd>
            <dt>Status</dt>
            <dd>Adult</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithoutValueTag(): void
    {
        $html = DetailView::widget()
            ->data(['name' => 'John', 'age' => 30])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'age', label: 'Age'),
            )
            ->valueTag(null)
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Name</dt>
            John
            <dt>Age</dt>
            30
            </dl>
            HTML,
            $html,
        );
    }

    public function testWithValueAndLabelContainingHtml(): void
    {
        $html = DetailView::widget()
            ->data(['name' => '<bold>'])
            ->fields(
                new DataField(property: 'name', label: 'Name'),
                new DataField(property: 'name', label: '<i>Name</i>'),
                new DataField(property: 'name', label: '<i>Name</i>', labelEncode: false),
                new DataField(property: 'name', label: 'Name', valueEncode: false),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>Name</dt>
            <dd>&lt;bold&gt;</dd>
            <dt>&lt;i&gt;Name&lt;/i&gt;</dt>
            <dd>&lt;bold&gt;</dd>
            <dt><i>Name</i></dt>
            <dd>&lt;bold&gt;</dd>
            <dt>Name</dt>
            <dd><bold></dd>
            </dl>
            HTML,
            $html,
        );
    }

    public static function dataValueEncode(): iterable
    {
        yield ['&lt;b&gt;Bold&lt;/b&gt;', '<b>Bold</b>', null];
        yield ['&lt;b&gt;Bold&lt;/b&gt;', '<b>Bold</b>', true];
        yield ['<b>Bold</b>', '<b>Bold</b>', false];
        yield ['&lt;b&gt;Bold&lt;/b&gt;', new StringableObject('<b>Bold</b>'), null];
        yield ['&lt;b&gt;Bold&lt;/b&gt;', new StringableObject('<b>Bold</b>'), true];
        yield ['<b>Bold</b>', new StringableObject('<b>Bold</b>'), false];
        yield ['<b>Bold</b>', NoEncode::string('<b>Bold</b>'), null];
        yield ['&lt;b&gt;Bold&lt;/b&gt;', NoEncode::string('<b>Bold</b>'), true];
        yield ['<b>Bold</b>', NoEncode::string('<b>Bold</b>'), false];
    }

    #[DataProvider('dataValueEncode')]
    public function testValueEncode(string $expected, mixed $content, ?bool $encode): void
    {
        $html = DetailView::widget()
            ->data(['name' => $content])
            ->fields(
                new DataField(property: 'name', valueEncode: $encode),
            )
            ->render();

        $this->assertSame(
            <<<HTML
            <dl>
            <dt>name</dt>
            <dd>$expected</dd>
            </dl>
            HTML,
            $html,
        );
    }

    public function testContainerTagWithEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        DetailView::widget()->containerTag('');
    }

    public function testFieldTagWithEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        DetailView::widget()->fieldTag('');
    }

    public function testListTagWithEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        DetailView::widget()->listTag('');
    }

    public function testLabelTagWithEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        DetailView::widget()->labelTag('');
    }

    public function testValueTagWithEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Tag name cannot be empty.');
        DetailView::widget()->valueTag('');
    }

    public function testImmutability(): void
    {
        $widget = DetailView::widget();
        $this->assertNotSame($widget, $widget->data([]));
        $this->assertNotSame($widget, $widget->fields());
        $this->assertNotSame($widget, $widget->containerTag(null));
        $this->assertNotSame($widget, $widget->containerAttributes([]));
        $this->assertNotSame($widget, $widget->prepend());
        $this->assertNotSame($widget, $widget->append());
        $this->assertNotSame($widget, $widget->listTag(null));
        $this->assertNotSame($widget, $widget->listAttributes([]));
        $this->assertNotSame($widget, $widget->fieldTag(null));
        $this->assertNotSame($widget, $widget->fieldAttributes([]));
        $this->assertNotSame($widget, $widget->fieldPrepend());
        $this->assertNotSame($widget, $widget->fieldAppend());
        $this->assertNotSame($widget, $widget->fieldTemplate(''));
        $this->assertNotSame($widget, $widget->labelTag(null));
        $this->assertNotSame($widget, $widget->labelAttributes([]));
        $this->assertNotSame($widget, $widget->labelPrepend());
        $this->assertNotSame($widget, $widget->labelAppend());
        $this->assertNotSame($widget, $widget->valueTag(null));
        $this->assertNotSame($widget, $widget->valueAttributes([]));
        $this->assertNotSame($widget, $widget->valuePrepend());
        $this->assertNotSame($widget, $widget->valueAppend());
        $this->assertNotSame($widget, $widget->valuePresenter(new SimpleValuePresenter()));
    }
}
