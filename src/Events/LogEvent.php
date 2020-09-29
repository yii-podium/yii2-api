<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\LogRepositoryInterface;
use yii\base\Event;

class LogEvent extends Event
{
    /**
     * @var bool whether action can be logged
     */
    public bool $canLog = true;

    public ?LogRepositoryInterface $repository = null;
}
