# CSP and inline JavaScript

Some Yii DataView widgets render their behavior as inline JavaScript — an `onChange`/`onchange` HTML attribute.
Under a strict [Content-Security-Policy](https://developer.mozilla.org/docs/Web/HTTP/Guides/CSP) `script-src`
(without `unsafe-inline`), the browser blocks the execution, and the widget stops working without any user-facing feedback.

## Overriding from `GridView`/`ListView`

`GridView`/`ListView`'s `useInlineJs(?bool $enabled)` overrides the setting for every `UseInlineJsInterface`
widget involved in rendering — the page size widget and, for `GridView`, every filter widget — regardless of
what each widget was configured with:

```php
->useInlineJs(false)
```

`null` (the default) leaves each widget's own setting untouched.

## Registering the delegated JavaScript

With inline JavaScript disabled, include the delegated `change` listener shipped at
`vendor/yiisoft/yii-dataview/assets/no-inline-js.js` on the page, for example with a plain `<script>` tag or
through your asset pipeline of choice:

```html
<script src="/path/to/no-inline-js.js"></script>
```

Since this is an external script, make sure the URL it is served from is allowed by your `script-src` policy.

It listens for `change` events on the document and reacts to the marker attributes rendered by the built-in
widgets below. A custom widget implementing `UseInlineJsInterface` needs its own listener for its own marker
attribute.

## `UseInlineJsInterface`

`\Yiisoft\Yii\DataView\UseInlineJsInterface` is the contract for a widget that can toggle inline JavaScript on
or off:

```php
public function useInlineJs(bool $enabled): static;
```

- `true` (the default) — the widget renders inline JavaScript, as before.
- `false` — the widget renders a marker attribute instead and relies on JavaScript registered separately, see
  [Registering the delegated JavaScript](#registering-the-delegated-javascript).

`\Yiisoft\Yii\DataView\UseInlineJsTrait` implements this contract. A custom widget only needs `use
UseInlineJsTrait;` and to check `$this->useInlineJs` when rendering.

## Built-in widgets

Three built-in widgets implement `UseInlineJsInterface`:

| Widget | Inline behavior |
|---|---|
| `\Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter` | Submits the filter form. |
| `\Yiisoft\Yii\DataView\PageSize\SelectPageSize` | Redirects to the URL for the selected page size. |
| `\Yiisoft\Yii\DataView\PageSize\InputPageSize` | Same as `SelectPageSize`. |

```php
->pageSizeWidget(SelectPageSize::widget()->useInlineJs(false))
```

```php
new DataColumn('status', filter: DropdownFilter::widget()->useInlineJs(false))
```
