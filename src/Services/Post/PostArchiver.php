<?php

declare(strict_types=1);

namespace Podium\Api\Services\Post;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\ArchiveEvent;
use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class PostArchiver extends Component implements ArchiverInterface
{
    public const EVENT_BEFORE_ARCHIVING = 'podium.post.archiving.before';
    public const EVENT_AFTER_ARCHIVING = 'podium.post.archiving.after';
    public const EVENT_BEFORE_REVIVING = 'podium.post.reviving.before';
    public const EVENT_AFTER_REVIVING = 'podium.post.reviving.after';

    /**
     * Calls before archiving the post.
     */
    private function beforeArchive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_ARCHIVING, $event);

        return $event->canArchive;
    }

    /**
     * Archives the post.
     */
    public function archive(RepositoryInterface $post): PodiumResponse
    {
        if (!$post instanceof PostRepositoryInterface || !$this->beforeArchive()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($post->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'post.already.archived')]);
            }

            if (!$post->archive()) {
                throw new ServiceException($post->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while archiving post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterArchive($post);

        return PodiumResponse::success();
    }

    /**
     * Calls after archiving post successfully.
     */
    private function afterArchive(PostRepositoryInterface $post): void
    {
        $this->trigger(self::EVENT_AFTER_ARCHIVING, new ArchiveEvent(['repository' => $post]));
    }

    /**
     * Calls before reviving the post.
     */
    private function beforeRevive(): bool
    {
        $event = new ArchiveEvent();
        $this->trigger(self::EVENT_BEFORE_REVIVING, $event);

        return $event->canRevive;
    }

    /**
     * Revives the post.
     */
    public function revive(RepositoryInterface $post): PodiumResponse
    {
        if (!$post instanceof PostRepositoryInterface || !$this->beforeRevive()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$post->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'post.not.archived')]);
            }

            if (!$post->revive()) {
                throw new ServiceException($post->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while reviving post', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRevive($post);

        return PodiumResponse::success();
    }

    /**
     * Calls after reviving the post successfully.
     */
    private function afterRevive(PostRepositoryInterface $post): void
    {
        $this->trigger(self::EVENT_AFTER_REVIVING, new ArchiveEvent(['repository' => $post]));
    }
}
