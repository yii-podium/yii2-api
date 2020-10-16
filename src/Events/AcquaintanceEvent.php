<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use yii\base\Event;

class AcquaintanceEvent extends Event
{
    /**
     * @var bool whether member and target can be friends
     */
    public bool $canBeFriends = true;

    /**
     * @var bool whether member can ignore target
     */
    public bool $canIgnore = true;

    /**
     * @var bool whether member can disconnect target
     */
    public bool $canDisconnect = true;

    public ?AcquaintanceRepositoryInterface $repository = null;
}
