<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class BuildEvent extends Event
{
    /**
     * @var bool whether repository can be created
     */
    public bool $canCreate = true;

    /**
     * @var bool whether repository can be edited
     */
    public bool $canEdit = true;

    public ?RepositoryInterface $repository = null;
}
