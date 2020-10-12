<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class PinEvent extends Event
{
    /**
     * @var bool whether repository can be pinned
     */
    public bool $canPin = true;

    /**
     * @var bool whether repository can be unpinned
     */
    public bool $canUnpin = true;

    public ?RepositoryInterface $repository = null;
}
