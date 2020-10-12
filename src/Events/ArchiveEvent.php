<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\RepositoryInterface;
use yii\base\Event;

class ArchiveEvent extends Event
{
    /**
     * @var bool whether repository can be archived
     */
    public bool $canArchive = true;

    /**
     * @var bool whether repository can be revived
     */
    public bool $canRevive = true;

    public ?RepositoryInterface $repository = null;
}
