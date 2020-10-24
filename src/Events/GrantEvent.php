<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class GrantEvent extends Event
{
    /**
     * @var bool whether role can be granted
     */
    public bool $canGrant = true;

    /**
     * @var bool whether role can be revoked
     */
    public bool $canRevoke = true;

    public ?RepositoryInterface $repository = null;
}
