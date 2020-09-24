<?php

declare(strict_types=1);

namespace Podium\Api\Events;

use Podium\Api\Interfaces\BookmarkRepositoryInterface;
use yii\base\Event;

class BookmarkEvent extends Event
{
    /**
     * @var bool whether model can be marked
     */
    public bool $canMark = true;

    public ?BookmarkRepositoryInterface $repository = null;
}
