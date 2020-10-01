<?php

declare(strict_types=1);

namespace Podium\Api\Services\Forum;

use InvalidArgumentException;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\MoveEvent;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class ForumMover extends Component implements MoverInterface
{
    public const EVENT_BEFORE_MOVING = 'podium.forum.moving.before';
    public const EVENT_AFTER_MOVING = 'podium.forum.moving.after';

    /**
     * Calls before moving the forum.
     */
    private function beforeMove(): bool
    {
        $event = new MoveEvent();
        $this->trigger(self::EVENT_BEFORE_MOVING, $event);

        return $event->canMove;
    }

    /**
     * Moves the forum to another category.
     */
    public function move(RepositoryInterface $forum, RepositoryInterface $category): PodiumResponse
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

        if (!$category instanceof CategoryRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeMove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$forum->move($category)) {
                throw new ServiceException($forum->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while moving forum', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterMove($forum);

        return PodiumResponse::success();
    }

    /**
     * Calls after moving the forum successfully.
     */
    private function afterMove(ForumRepositoryInterface $forum): void
    {
        $this->trigger(self::EVENT_AFTER_MOVING, new MoveEvent(['repository' => $forum]));
    }
}
