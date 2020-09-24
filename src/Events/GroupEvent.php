<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\GroupMemberRepositoryInterface;
use yii\base\Event;

class GroupEvent extends Event
{
    /**
     * @var bool whether model can be joined
     */
    public bool $canJoin = true;

    /**
     * @var bool whether model can be left
     */
    public bool $canLeave = true;

    public ?GroupMemberRepositoryInterface $repository = null;
}
