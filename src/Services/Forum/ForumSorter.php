<?php

declare(strict_types=1);

namespace Podium\Api\Services\Forum;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\SortEvent;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\SorterInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class ForumSorter extends Component implements SorterInterface
{
    public const EVENT_BEFORE_REPLACING = 'podium.forum.replacing.before';
    public const EVENT_AFTER_REPLACING = 'podium.forum.replacing.after';
    public const EVENT_BEFORE_SORTING = 'podium.forum.sorting.before';
    public const EVENT_AFTER_SORTING = 'podium.forum.sorting.after';

    /**
     * Calls before replacing the order of forums.
     */
    private function beforeReplace(): bool
    {
        $event = new SortEvent();
        $this->trigger(self::EVENT_BEFORE_REPLACING, $event);

        return $event->canReplace;
    }

    /**
     * Replaces the spot of the forums.
     */
    public function replace(RepositoryInterface $firstForum, RepositoryInterface $secondForum): PodiumResponse
    {
        if (
            !$firstForum instanceof ForumRepositoryInterface
            || !$secondForum instanceof ForumRepositoryInterface
            || !$this->beforeReplace()
        ) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldOrder = $firstForum->getOrder();
            if (!$firstForum->setOrder($secondForum->getOrder())) {
                throw new Exception('Error while setting new forum order!');
            }
            if (!$secondForum->setOrder($oldOrder)) {
                throw new Exception('Error while setting new forum order!');
            }

            $transaction->commit();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(
                ['Exception while replacing forums order', $exc->getMessage(), $exc->getTraceAsString()],
                'podium'
            );

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterReplace();

        return PodiumResponse::success();
    }

    /**
     * Calls after replacing the forums order successfully.
     */
    private function afterReplace(): void
    {
        $this->trigger(self::EVENT_AFTER_REPLACING);
    }

    /**
     * Calls before sorting the forums.
     */
    private function beforeSort(): bool
    {
        $event = new SortEvent();
        $this->trigger(self::EVENT_BEFORE_SORTING, $event);

        return $event->canSort;
    }

    /**
     * Sorts the forums.
     */
    public function sort(RepositoryInterface $forum): PodiumResponse
    {
        if (!$forum instanceof ForumRepositoryInterface || !$this->beforeSort()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$forum->sort()) {
                throw new ServiceException($forum->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while sorting forums', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterSort();

        return PodiumResponse::success();
    }

    /**
     * Calls after sorting forums successfully.
     */
    private function afterSort(): void
    {
        $this->trigger(self::EVENT_AFTER_SORTING);
    }
}
