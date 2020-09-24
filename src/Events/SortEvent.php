<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use yii\base\Event;

class SortEvent extends Event
{
    /**
     * @var bool whether models order can be replaced
     */
    public bool $canReplace = true;

    /**
     * @var bool whether models can be sorted
     */
    public bool $canSort = true;
}
