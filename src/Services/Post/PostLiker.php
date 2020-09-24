<?php

declare(strict_types=1);

namespace Podium\Api\Services\Post;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\ThumbEvent;
use Podium\Api\Interfaces\LikerInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThumbRepositoryInterface;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class PostLiker extends Component implements LikerInterface
{
    public const EVENT_BEFORE_THUMB_UP = 'podium.thumb.up.before';
    public const EVENT_AFTER_THUMB_UP = 'podium.thumb.up.after';
    public const EVENT_BEFORE_THUMB_DOWN = 'podium.thumb.down.before';
    public const EVENT_AFTER_THUMB_DOWN = 'podium.thumb.down.after';
    public const EVENT_BEFORE_THUMB_RESET = 'podium.thumb.reset.before';
    public const EVENT_AFTER_THUMB_RESET = 'podium.thumb.reset.after';

    /**
     * Calls before giving thumb up.
     */
    public function beforeThumbUp(): bool
    {
        $event = new ThumbEvent();
        $this->trigger(self::EVENT_BEFORE_THUMB_UP, $event);

        return $event->canThumbUp;
    }

    /**
     * Gives thumb up to the post.
     */
    public function thumbUp(
        ThumbRepositoryInterface $thumb,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse {
        if (!$this->beforeThumbUp()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $rated = true;
            if (!$thumb->fetchOne($member, $post)) {
                $thumb->prepare($member, $post);
                $rated = false;
            }
            if ($thumb->isUp()) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'post.already.liked')]);
            }

            if (!$thumb->up()) {
                return PodiumResponse::error($thumb->getErrors());
            }
            if ($rated && !$post->updateCounters(1, -1)) {
                throw new Exception('Error while updating post counters!');
            }
            if (!$rated && !$post->updateCounters(1, 0)) {
                throw new Exception('Error while updating post counters!');
            }

            $this->afterThumbUp($thumb);
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while giving thumb up', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after giving thumb up successfully.
     */
    public function afterThumbUp(ThumbRepositoryInterface $thumb): void
    {
        $this->trigger(self::EVENT_AFTER_THUMB_UP, new ThumbEvent(['repository' => $thumb]));
    }

    /**
     * Calls before giving thumb down.
     */
    public function beforeThumbDown(): bool
    {
        $event = new ThumbEvent();
        $this->trigger(self::EVENT_BEFORE_THUMB_DOWN, $event);

        return $event->canThumbDown;
    }

    /**
     * Gives thumb down to the post.
     */
    public function thumbDown(
        ThumbRepositoryInterface $thumb,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse {
        if (!$this->beforeThumbDown()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $rated = true;
            if (!$thumb->fetchOne($member, $post)) {
                $thumb->prepare($member, $post);
                $rated = false;
            }
            if ($thumb->isDown()) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'post.already.disliked')]);
            }

            if (!$thumb->down()) {
                return PodiumResponse::error($thumb->getErrors());
            }
            if ($rated && !$post->updateCounters(-1, 1)) {
                throw new Exception('Error while updating post counters!');
            }
            if (!$rated && !$post->updateCounters(0, 1)) {
                throw new Exception('Error while updating post counters!');
            }

            $this->afterThumbDown($thumb);
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while giving thumb down', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after giving thumb down to the post.
     */
    public function afterThumbDown(ThumbRepositoryInterface $thumb): void
    {
        $this->trigger(self::EVENT_AFTER_THUMB_DOWN, new ThumbEvent(['repository' => $thumb]));
    }

    /**
     * Calls before resetting thumb.
     */
    public function beforeThumbReset(): bool
    {
        $event = new ThumbEvent();
        $this->trigger(self::EVENT_BEFORE_THUMB_RESET, $event);

        return $event->canThumbReset;
    }

    /**
     * Resets thumb for the post.
     */
    public function thumbReset(
        ThumbRepositoryInterface $thumb,
        PostRepositoryInterface $post,
        MemberRepositoryInterface $member
    ): PodiumResponse {
        if (!$this->beforeThumbReset()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$thumb->fetchOne($member, $post)) {
                return PodiumResponse::error(['api' => Yii::t('podium.error', 'post.not.rated')]);
            }

            $isUp = $thumb->isUp();

            if (!$thumb->reset()) {
                return PodiumResponse::error();
            }
            if ($isUp && !$post->updateCounters(-1, 0)) {
                throw new Exception('Error while updating post counters!');
            }
            if (!$isUp && !$post->updateCounters(0, -1)) {
                throw new Exception('Error while updating post counters!');
            }

            $this->afterThumbReset();
            $transaction->commit();

            return PodiumResponse::success();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while resetting thumb', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }
    }

    /**
     * Calls after resetting thumb for the post successfully.
     */
    public function afterThumbReset(): void
    {
        $this->trigger(self::EVENT_AFTER_THUMB_RESET);
    }
}
