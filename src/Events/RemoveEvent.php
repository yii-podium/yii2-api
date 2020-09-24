<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use yii\base\Event;

class RemoveEvent extends Event
{
    /**
     * @var bool whether model can be removed
     */
    public bool $canRemove = true;
}
