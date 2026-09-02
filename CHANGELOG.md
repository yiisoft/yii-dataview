# Yii DataView Change Log

## 1.2.0 September 02, 2026

- New #347, #352: Add `prepareDataReader()` to reuse filtered data reader (@samdark, @vjik)
- New #355: Add `UseInlineJsInterface::useInlineJs()` to `DropdownFilter`, `SelectPageSize` and
  `InputPageSize`, and `BaseListView::useInlineJs()` to override it for every rendered widget, so their
  `onChange`/`onchange` handler can be replaced with the shipped `no-inline-js.js` script (@vjik)
- Enh #338: Pass `DataContext` to `callable` buttons in `ActionColumn` (@vjik)

## 1.1.0 March 21, 2026

- New #329: Add caption support for GridView (@Roc755)
- Enh #328: Explicitly import constants in "use" section (@mspirkov)
- Enh #331: Bump minimal `yiisoft/html` version to `3.13` and add support for `^4.0` (@vjik)

## 1.0.0 December 14, 2025

- Initial release.
