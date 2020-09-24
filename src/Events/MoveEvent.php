<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class MoveEvent extends Event
{
    /**
     * @var bool whether model can be moved
     */
    public bool $canMove = true;

    public ?RepositoryInterface $repository = null;
}
