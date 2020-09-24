<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\MessengerInterface;
use yii\base\Event;

class MessageEvent extends Event
{
    /**
     * @var bool whether model can be created
     */
    public bool $canSend = true;

    /**
     * @var MessengerInterface|null
     */
    public ?MessengerInterface $model = null;
}
