<?php

declare(strict_types=1);

namespace Podium\Api\Services\Thread;

use Podium\Api\Events\BookmarkEvent;
use Podium\Api\Interfaces\BookmarkerInterface;
use Podium\Api\Interfaces\BookmarkRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ThreadBookmarker extends Component implements BookmarkerInterface
{
    public const EVENT_BEFORE_MARKING = 'podium.bookmark.marking.before';
    public const EVENT_AFTER_MARKING = 'podium.bookmark.marking.after';

    /**
     * Calls before marking the thread.
     */
    private function beforeMark(): bool
    {
        $event = new BookmarkEvent();
        $this->trigger(self::EVENT_BEFORE_MARKING, $event);

        return $event->canMark;
    }

    /**
     * Bookmarks the thread.
     */
    public function mark(
        BookmarkRepositoryInterface $bookmark,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse {
        if (!$this->beforeMark()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /** @var ThreadRepositoryInterface $thread */
            $thread = $post->getParent();
            if (!$bookmark->fetchOne($member, $thread)) {
                $bookmark->prepare($member, $thread);
            }

            $postCreatedTime = $post->getCreatedAt();
            if ($bookmark->getLastSeen() < $postCreatedTime && !$bookmark->mark($postCreatedTime)) {
                throw new ServiceException($bookmark->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while bookmarking thread', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterMark($bookmark);

        return PodiumResponse::success();
    }

    /**
     * Calls after marking the thread successfully.
     */
    private function afterMark(BookmarkRepositoryInterface $bookmark): void
    {
        $this->trigger(self::EVENT_AFTER_MARKING, new BookmarkEvent(['repository' => $bookmark]));
    }
}
