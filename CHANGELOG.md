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
- New #363: Add `rowHeader` parameter to `DataColumn` to render body cells as `<th>` row headers (@vjik)
- New #363: Add `BaseListView::accessibility()` that opts into automatically added accessibility attributes:
  `scope="col"` and `aria-sort` on `GridView` header cells, `scope="row"` on row header cells, and
  `aria-current`/`aria-disabled` on pagination links (@vjik)
- New #363: Add `$enableAccessibility` parameter to the `PaginationContext` constructor and to
  `OffsetPagination::create()` and `KeysetPagination::create()` (@vjik)
- Enh #358: Make dependency container in `GridView` constructor optional (@vjik)

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
