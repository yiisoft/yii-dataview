# Yii DataView Change Log

## 1.2.1 under development

- New #359: Add `GridView::keepColumnAttributesInEmptyCell()` to keep column body cell attributes on empty cells (@vjik)
- New #361: Add `GridView` methods `sortableHeaderClass()`, `sortableHeaderAscClass()`, `sortableHeaderDescClass()`,
  `sortableLinkAscClass()` and `sortableLinkDescClass()` to configure CSS classes for sortable column headers and 
  links (@vjik)
- New #360: Add `GridView::filterRowAttributes()`, and `filterAttributes` and `filterClass` parameters to `DataColumn`
  to set HTML attributes and a CSS class for filter cells (@vjik)
- New #362: Add `GridView::captionAttributes()` method and `$attributes` parameter to `GridView::caption()` to set
  HTML attributes for the `caption` tag (@vjik)
- Enh #358: Make dependency container in `GridView` constructor optional (@vjik)
- Bug #363: Render disabled `KeysetPagination` controls ("previous" on the first page, "next" on the last page) as
  `span` elements instead of `a` elements without an `href` (@vjik)
- Bug #364: Render disabled `OffsetPagination` controls ("first"/"previous" on the first page, "next"/"last" on the
  last page) as `span` elements instead of clickable `a` elements (@vjik)
- Bug #365: Keep `GridView` and column header cell attributes when a column renders no header (@vjik)

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
