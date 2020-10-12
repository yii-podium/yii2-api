<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use yii\base\Event;

class SortEvent extends Event
{
    /**
     * @var bool whether repositories order can be replaced
     */
    public bool $canReplace = true;

    /**
     * @var bool whether repositories can be sorted
     */
    public bool $canSort = true;
}
