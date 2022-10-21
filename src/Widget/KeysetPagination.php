<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use Yiisoft\Definitions\Exception\CircularReferenceException;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\Definitions\Exception\NotInstantiableException;
use Yiisoft\Factory\NotFoundException;
use Yiisoft\Html\Tag\Nav;
use Yiisoft\Yii\Widgets\Menu;

use function array_filter;
use function array_key_exists;

final class KeysetPagination extends BasePagination
{
    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    protected function run(): string
    {
        return $this->renderPagination();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     * @throws CircularReferenceException
     */
    private function renderPagination(): string
    {
        $attributes = $this->getAttributes();
        $items = [];

        $items[] = $this->renderPreviousPageNavLink();
        $items[] = $this->renderNextPageNavLink();

        if (!array_key_exists('aria-label', $attributes)) {
            $attributes['aria-label'] = 'Pagination';
        }

        return
            Nav::tag()
                ->addAttributes($attributes)
                ->content(
                    PHP_EOL .
                    Menu::widget()
                        ->class($this->getMenuClass())
                        ->items(array_filter($items))
                        ->itemsContainerClass($this->getMenuItemContainerClass())
                        ->linkClass($this->getMenuItemLinkClass()) .
                    PHP_EOL
                )
                ->encode(false)
                ->render();
    }

    private function renderPreviousPageNavLink(): array
    {
        $items = [];
        $iconContainerAttributes = $this->getIconContainerAttributes();

        if (!array_key_exists('aria-hidden', $iconContainerAttributes)) {
            $iconContainerAttributes['aria-hidden'] = 'true';
        }

        if (
            $this->getLabelPreviousPage() !== '' ||
            $this->getIconPreviousPage() !== '' ||
            $this->getIconClassPreviousPage() !== ''
        ) {
            $paginator = $this->getPaginator();
            $token = (int) $paginator->getPreviousPageToken();

            $disabled = $token === 0;

            if ($token > 0) {
                $paginator = $paginator->withPreviousPageToken((string) ($token - 1));
            }

            $items = [
                'disabled' => $disabled,
                'icon' => $this->getIconPreviousPage(),
                'iconAttributes' => $this->getIconAttributes(),
                'iconClass' => $this->getIconClassPreviousPage(),
                'iconContainerAttributes' => $iconContainerAttributes,
                'label' => $this->getLabelPreviousPage(),
                'link' => $this->createUrl((int) $paginator->getPreviousPageToken()),
            ];
        }

        return $items;
    }

    private function renderNextPageNavLink(): array
    {
        $paginator = $this->getPaginator();

        $iconContainerAttributes = $this->getIconContainerAttributes();

        if (!array_key_exists('aria-hidden', $iconContainerAttributes)) {
            $iconContainerAttributes['aria-hidden'] = 'true';
        }

        return [
            'disabled' => $paginator->getNextPageToken() === null,
            'icon' => $this->getIconNextPage(),
            'iconAttributes' => $this->getIconAttributes(),
            'iconClass' => $this->getIconClassNextPage(),
            'iconContainerAttributes' => $iconContainerAttributes,
            'label' => $this->getLabelNextPage(),
            'link' => $this->createUrl((int) $paginator->getNextPageToken()),
        ];
    }
}
