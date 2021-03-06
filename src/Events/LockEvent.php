<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class LockEvent extends Event
{
    /**
     * @var bool whether repository can be locked
     */
    public bool $canLock = true;

    /**
     * @var bool whether repository can be unlocked
     */
    public bool $canUnlock = true;

    public ?RepositoryInterface $repository = null;
}
