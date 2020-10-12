<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\GroupMemberRepositoryInterface;
use yii\base\Event;

class GroupEvent extends Event
{
    /**
     * @var bool whether repository can be joined
     */
    public bool $canJoin = true;

    /**
     * @var bool whether repository can be left
     */
    public bool $canLeave = true;

    public ?GroupMemberRepositoryInterface $repository = null;
}
