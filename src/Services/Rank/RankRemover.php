<?php

declare(strict_types=1);

namespace Podium\Api\Services\Rank;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;

final class RankRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.rank.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.rank.removing.after';

    /**
     * Calls before removing the rank.
     */
    public function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the rank.
     */
    public function remove(RepositoryInterface $rank): PodiumResponse
    {
        if (!$rank instanceof RankRepositoryInterface || !$this->beforeRemove()) {
            return PodiumResponse::error();
        }

        try {
            if (!$rank->delete()) {
                return PodiumResponse::error();
            }

            $this->afterRemove();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            Yii::error(['Exception while removing rank', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after removing the rank successfully.
     */
    public function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
