<?php

declare(strict_types=1);

namespace Yiisoft\Yii\DataView;

use Yiisoft\Widget\Widget as BaseWidget;

use function strpos;

abstract class Widget extends BaseWidget
{
    private ?string $id = null;
    private bool $autoGenerate = true;
    private string $autoIdPrefix = 'w';
    private static int $counter = 0;

    /**
     * Set the Id of the widget.
     *
     * @param string $value
     *
     * @return static
     */
    public function id(string $value): self
    {
        $new = clone $this;
        $new->id = $value;
        return $new;
    }

    /**
     * Counter used to generate {@see id} for widgets.
     *
     * @param int $value
     */
    public static function counter(int $value): void
    {
        self::$counter = $value;
    }

    /**
     * The prefix to the automatically generated widget IDs.
     *
     * @param string $value
     *
     * @return static
     *
     * {@see getId()}
     */
    public function autoIdPrefix(string $value): self
    {
        $new = clone $this;
        $new->autoIdPrefix = $value;
        return $new;
    }

    /**
     * Returns the Id of the widget.
     *
     * @return string|null Id of the widget.
     */
    protected function getId(): ?string
    {
        if ($this->autoGenerate && $this->id === null) {
            $this->id = $this->autoIdPrefix . ++self::$counter;
        }

        return $this->id;
    }
}
