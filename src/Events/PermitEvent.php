<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\AllowerInterface;
use yii\base\Event;

class PermitEvent extends Event
{
    /**
     * @var bool whether permit can be checked
     */
    public bool $canCheck = true;

    public ?AllowerInterface $allower = null;
}
