<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class HideEvent extends Event
{
    /**
     * @var bool whether repository can be hidden
     */
    public bool $canHide = true;

    /**
     * @var bool whether repository can be revealed
     */
    public bool $canReveal = true;

    public ?RepositoryInterface $repository = null;
}
