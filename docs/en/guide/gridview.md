# Grid view

Grid view is the most powerful data widget provided by Yii. 
It provides a flexible way to render data in a grid format, with support for pagination,
sorting, custom item rendering, sorting, extra action buttons, and more.

// TODO: add screenshot!

## Basic Usage

The basic usage is the following:

```php

```


## Data columns



## Action buttons

## Checkboxes


## Filters

filterCellAttributes
filterCellInvalidClass
filterErrorsContainerAttributes

## Sorting

keepPageOnSort



## Defining your own columns

column renderers


addColumnRendererConfigs
ContainerInterface $columnRenderersDependencyContainer,
$this->columnRendererContainer = new RendererContainer($columnRenderersDependencyContainer);

public function addColumnRendererConfigs(array $configs): self

## Rendering options

The widget rendering could be customized.

### Additional customization

As for every data widget that renders a set of data, you can additionally customize it with:

- [Pagination](pagination.md)
- [URLs](urls.md)
- [Translation](translation.md)
- [Themes](themes.md)
