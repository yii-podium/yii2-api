<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class BanEvent extends Event
{
    /**
     * @var bool whether member can be banned
     */
    public bool $canBan = true;

    /**
     * @var bool whether member can be unbanned
     */
    public bool $canUnban = true;

    public ?RepositoryInterface $repository = null;
}
