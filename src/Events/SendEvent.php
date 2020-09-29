<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class SendEvent extends Event
{
    /**
     * @var bool whether message can be sent
     */
    public bool $canSend = true;

    public ?RepositoryInterface $repository = null;
}
