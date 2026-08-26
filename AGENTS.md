# Agents

## JavaScript

`DropdownFilter`, `SelectPageSize`, and `InputPageSize` each render their behavior twice:

- as inline JavaScript, in the `onChange`/`onchange` attribute value built in the widget's PHP code
  (`src/Filter/Widget/DropdownFilter.php`, `src/PageSize/SelectPageSize.php`, `src/PageSize/InputPageSize.php`),
  used when `useInlineJs()` is `true` (the default);
- as delegated JavaScript, in `assets/no-inline-js.js`, used when `useInlineJs(false)` is set.

Both must stay logically equivalent. When changing the behavior in one, update the other to match.
