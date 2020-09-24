<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use yii\base\Event;

class SubscriptionEvent extends Event
{
    /**
     * @var bool whether model can be subscribed
     */
    public bool $canSubscribe = true;

    /**
     * @var bool whether model can be unsubscribed
     */
    public bool $canUnsubscribe = true;

    public ?SubscriptionRepositoryInterface $repository = null;
}
