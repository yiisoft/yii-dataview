<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Widget;

use Yiisoft\Yii\DataView\Widget;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;

abstract class AbstractLinkWidget extends Widget
{
    protected ?array $requestArguments = null;
    protected ?array $requestQueryParams = null;
    protected string $pageParam = 'page';
    protected ?bool $pageArgument = null;
    protected CurrentRoute $currentRoute;
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct(
        CurrentRoute $currentRoute,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->currentRoute = $currentRoute;
        $this->urlGenerator = $urlGenerator;
    }

    protected function beforeRun(): bool
    {
        if ($this->pageArgument === null) {
            $this->pageArgument = array_key_exists($this->pageParam, $this->currentRoute->getArguments());
        }

        if ($this->requestArguments === null) {
            $this->requestArguments = $this->currentRoute->getArguments();
        }

        if ($this->requestQueryParams === null) {
            $this->requestQueryParams = [];

            if ($uri = $this->currentRoute->getUri()) {
                parse_str($uri->getQuery(), $this->requestQueryParams);
            }
        }

        return parent::beforeRun();
    }

    /**
     * Set/clear request arguments
     *
     * @param array|null $requestArguments
     *
     * @return self
     *
     * {@see UrlGeneratorInterface::generate()} for detail
     */
    public function requestArguments(?array $requestArguments): self
    {
        $new = clone $this;
        $new->requestArguments = $requestArguments;

        return $new;
    }

    /**
     * Set/clear query parameters for url
     *
     * @param array|null $requestQueryParams
     *
     * @return self
     *
     * {@see UrlGeneratorInterface::generate()} for detail
     */
    public function requestQueryParams(?array $requestQueryParams): self
    {
        $new = clone $this;
        $new->requestQueryParams = $requestQueryParams;

        return $new;
    }

    /**
     * Name of $_GET/Route argument page param using for pagination
     *
     * @param string $value
     *
     * @return self
     */
    public function pageParam(string $value): self
    {
        $new = clone $this;
        $new->pageParam = $value;

        return $new;
    }

    /**
     * Use route argument instead of $_GET param for page number, like /page-{pageParam:\d+}
     *
     * @param bool|null $value
     *
     * @return self
     */
    public function pageArgument(?bool $value = true): self
    {
        $new = clone $this;
        $new->pageArgument = $value;

        return $new;
    }
}
