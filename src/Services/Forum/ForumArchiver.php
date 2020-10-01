<?php

declare(strict_types=1);

namespace Podium\Api\Services\Forum;

use InvalidArgumentException;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\ArchiveEvent;
use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ForumArchiver extends Component implements ArchiverInterface
{
    public const EVENT_BEFORE_ARCHIVING = 'podium.forum.archiving.before';
    public const EVENT_AFTER_ARCHIVING = 'podium.forum.archiving.after';
    public const EVENT_BEFORE_REVIVING = 'podium.forum.reviving.before';
    public const EVENT_AFTER_REVIVING = 'podium.forum.reviving.after';

    /**
     * Calls before archiving the forum.
     */
    private function beforeArchive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_ARCHIVING, $event);

        return $event->canArchive;
    }

    /**
     * Archives the forum.
     */
    public function archive(RepositoryInterface $forum): PodiumResponse
    {
        if (!$forum instanceof ForumRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeArchive()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($forum->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'forum.already.archived')]);
            }

            if (!$forum->archive()) {
                throw new ServiceException($forum->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while archiving forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterArchive($forum);

        return PodiumResponse::success();
    }

    /**
     * Calls after archiving the forum successfully.
     */
    private function afterArchive(ForumRepositoryInterface $forum): void
    {
        $this->trigger(self::EVENT_AFTER_ARCHIVING, new ArchiveEvent(['repository' => $forum]));
    }

    /**
     * Calls before reviving the forum.
     */
    private function beforeRevive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_REVIVING, $event);

        return $event->canRevive;
    }

    /**
     * Revives the forum.
     */
    public function revive(RepositoryInterface $forum): PodiumResponse
    {
        if (!$forum instanceof ForumRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Forum must be instance of Podium\Api\Interfaces\ForumRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeRevive()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$forum->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'forum.not.archived')]);
            }

            if (!$forum->revive()) {
                throw new ServiceException($forum->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while reviving forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRevive($forum);

        return PodiumResponse::success();
    }

    /**
     * Calls after reviving the forum successfully.
     */
    private function afterRevive(ForumRepositoryInterface $forum): void
    {
        $this->trigger(self::EVENT_AFTER_REVIVING, new ArchiveEvent(['repository' => $forum]));
    }
}
