<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView\Tests;

use Yiisoft\Yii\DataView\Exception\InvalidConfigException;
use Yiisoft\Yii\DataView\ListView;

final class ListViewTest extends TestCase
{
    public function testAfterItemBeforeItem(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()
            ->beforeItem(static fn () => '<div class="testMe">')
            ->afterItem(static fn () => '</div>')
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div class="testMe">
        <div></div>
        </div>
        <div class="testMe">
        <div>1</div>
        </div>
        <div class="testMe">
        <div>2</div>
        </div>
        <div class="testMe">
        <div>3</div>
        </div>
        <div class="testMe">
        <div>4</div>
        </div>
        <div class="testMe">
        <div>5</div>
        </div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testGetViewPath(): void
    {
        $viewPath = ListView::widget()->getViewPath();
        $this->assertEquals($this->webView->getBasePath(), $viewPath);
    }

    public function testItemViewAsString(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()
            ->itemView('//_listview')
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div><div>1</div><div>tests 1</div><div>10</div>
        </div>
        <div><div>2</div><div>tests 2</div><div>20</div>
        </div>
        <div><div>3</div><div>tests 3</div><div>30</div>
        </div>
        <div><div>4</div><div>tests 4</div><div>40</div>
        </div>
        <div><div>5</div><div>tests 5</div><div>50</div>
        </div>
        <div><div>6</div><div>tests 6</div><div>60</div>
        </div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testItemViewAsCallable(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()
            ->itemView(
                static fn ($model) =>
                    '<div>' . $model['id'] . '</div>' .
                    '<div>' . $model['name'] . '</div>' .
                    '<div>' . $model['total'] . '</div>'
            )
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div><div>1</div><div>tests 1</div><div>10</div></div>
        <div><div>2</div><div>tests 2</div><div>20</div></div>
        <div><div>3</div><div>tests 3</div><div>30</div></div>
        <div><div>4</div><div>tests 4</div><div>40</div></div>
        <div><div>5</div><div>tests 5</div><div>50</div></div>
        <div><div>6</div><div>tests 6</div><div>60</div></div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testItemViewException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('itemView property should be string or callable.');
        ListView::widget()->itemView([]);
    }

    public function testItemOptions(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()
            ->itemView(
                static fn ($model) =>
                    '<div>' . $model['id'] . '</div>' .
                    '<div>' . $model['name'] . '</div>' .
                    '<div>' . $model['total'] . '</div>'
            )
            ->itemOptions(['class' => 'text-danger'])
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div class="text-danger"><div>1</div><div>tests 1</div><div>10</div></div>
        <div class="text-danger"><div>2</div><div>tests 2</div><div>20</div></div>
        <div class="text-danger"><div>3</div><div>tests 3</div><div>30</div></div>
        <div class="text-danger"><div>4</div><div>tests 4</div><div>40</div></div>
        <div class="text-danger"><div>5</div><div>tests 5</div><div>50</div></div>
        <div class="text-danger"><div>6</div><div>tests 6</div><div>60</div></div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());

        $listView = ListView::widget()
            ->itemView(
                static fn ($model) =>
                    '<div>' . $model['id'] . '</div>' .
                    '<div>' . $model['name'] . '</div>' .
                    '<div>' . $model['total'] . '</div>'
            )
            ->itemOptions(static fn ($model) => ['class' => $model['total'] === '40' ? 'text-success' : 'text-danger'])
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w2-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div class="text-danger"><div>1</div><div>tests 1</div><div>10</div></div>
        <div class="text-danger"><div>2</div><div>tests 2</div><div>20</div></div>
        <div class="text-danger"><div>3</div><div>tests 3</div><div>30</div></div>
        <div class="text-success"><div>4</div><div>tests 4</div><div>40</div></div>
        <div class="text-danger"><div>5</div><div>tests 5</div><div>50</div></div>
        <div class="text-danger"><div>6</div><div>tests 6</div><div>60</div></div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testItemOptionsException(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('itemOptions property must be either array or callable.');
        ListView::widget()->itemOptions('');
    }

    public function testOptions(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()
            ->itemView(
                static fn ($model) =>
                    '<div>' . $model['id'] . '</div>' .
                    '<div>' . $model['name'] . '</div>' .
                    '<div>' . $model['total'] . '</div>'
            )
            ->options(['class' => 'list-view', 'tag' => 'article'])
            ->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <article id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div><div>1</div><div>tests 1</div><div>10</div></div>
        <div><div>2</div><div>tests 2</div><div>20</div></div>
        <div><div>3</div><div>tests 3</div><div>30</div></div>
        <div><div>4</div><div>tests 4</div><div>40</div></div>
        <div><div>5</div><div>tests 5</div><div>50</div></div>
        <div><div>6</div><div>tests 6</div><div>60</div></div>
        </article>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testRender(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div></div>
        <div>1</div>
        <div>2</div>
        <div>3</div>
        <div>4</div>
        <div>5</div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());

        $listView = ListView::widget()->frameworkCss(ListView::BULMA)->paginator($this->createOffsetPaginator());

        $html = <<<'HTML'
        <div id="w2-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div data-key="0"></div>
        <div data-key="1">1</div>
        <div data-key="2">2</div>
        <div data-key="3">3</div>
        <div data-key="4">4</div>
        <div data-key="5">5</div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testPaginatorEmpty(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "paginator" property must be set.');
        ListView::widget()->render();
    }

    public function testSeparator(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()->paginator($this->createOffsetPaginator())->separator("\n");

        $html = <<<'HTML'
        <div id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div></div>
        <div>1</div>
        <div>2</div>
        <div>3</div>
        <div>4</div>
        <div>5</div>
        </div>
        HTML;

        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testViewParams(): void
    {
        ListView::counter(0);

        $listView = ListView::widget()
            ->itemView('//_listview_params')
            ->paginator($this->createOffsetPaginator())
            ->viewParams(['itemClass' => 'text-success']);

        $html = <<<'HTML'
        <div id="w1-listview" class="list-view"><div>Showing <b>1-6</b> of <b>6</b> items</div>
        <div><div class=text-success>1</div>
        </div>
        <div><div class=text-success>2</div>
        </div>
        <div><div class=text-success>3</div>
        </div>
        <div><div class=text-success>4</div>
        </div>
        <div><div class=text-success>5</div>
        </div>
        <div><div class=text-success>6</div>
        </div>
        </div>
        HTML;
        $this->assertEqualsWithoutLE($html, $listView->render());
    }

    public function testViewPath(): void
    {
        $viewPath = ListView::widget()->ViewPath(__DIR__ . '/runtime')->getViewPath();
        $this->assertEquals(__DIR__ . '/runtime', $viewPath);
    }
}
