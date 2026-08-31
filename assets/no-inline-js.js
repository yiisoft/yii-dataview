/**
 * Delegated `change` listener for widgets that render a marker attribute instead of an inline `onChange`/
 * `onchange` handler when `Yiisoft\Yii\DataView\UseInlineJsInterface::useInlineJs()` is disabled.
 */
document.addEventListener('change', (event) => {
    const target = event.target;

    if (!(target instanceof Element)) {
        return;
    }

    // SelectPageSize / InputPageSize: redirect to the URL for the selected page size
    if (target.hasAttribute('data-yii-dataview-page-size-onchange')) {
        window.location.href = target.value === target.dataset.defaultPageSize
            ? target.dataset.defaultUrl
            : target.dataset.urlPattern.replace('YII-DATAVIEW-PAGE-SIZE-PLACEHOLDER', target.value);
        return;
    }

    // DropdownFilter: submit the filter form
    if (target.hasAttribute('data-yii-dataview-dropdown-filter-onchange')) {
        target.form?.submit();
    }
});
