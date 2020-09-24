<?php

declare(strict_types=1);

namespace Podium\Api\Services\Category;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\ArchiveEvent;
use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;

final class CategoryArchiver extends Component implements ArchiverInterface
{
    public const EVENT_BEFORE_ARCHIVING = 'podium.category.archiving.before';
    public const EVENT_AFTER_ARCHIVING = 'podium.category.archiving.after';
    public const EVENT_BEFORE_REVIVING = 'podium.category.reviving.before';
    public const EVENT_AFTER_REVIVING = 'podium.category.reviving.after';

    /**
     * Calls before archiving the category.
     */
    public function beforeArchive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_ARCHIVING, $event);

        return $event->canArchive;
    }

    /**
     * Archives the category.
     */
    public function archive(RepositoryInterface $category): PodiumResponse
    {
        if (!$category instanceof CategoryRepositoryInterface || !$this->beforeArchive()) {
            return PodiumResponse::error();
        }

        try {
            if (!$category->archive()) {
                return PodiumResponse::error($category->getErrors());
            }

            $this->afterArchive($category);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while archiving category', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after successful archiving the category.
     */
    public function afterArchive(CategoryRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_ARCHIVING, new ArchiveEvent(['repository' => $category]));
    }

    /**
     * Calls before reviving the category.
     */
    public function beforeRevive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_REVIVING, $event);

        return $event->canRevive;
    }

    /**
     * Revives the category.
     */
    public function revive(RepositoryInterface $category): PodiumResponse
    {
        if (!$category instanceof CategoryRepositoryInterface || !$this->beforeRevive()) {
            return PodiumResponse::error();
        }

        try {
            if (!$category->revive()) {
                return PodiumResponse::error($category->getErrors());
            }

            $this->afterRevive($category);

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while reviving category', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after successful reviving the category.
     */
    public function afterRevive(CategoryRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_REVIVING, new ArchiveEvent(['repository' => $category]));
    }
}
