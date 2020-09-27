<?php

declare(strict_types=1);

namespace Podium\Api\Services\Member;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\AcquaintanceEvent;
use Podium\Api\Interfaces\AcquaintanceInterface;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MemberAcquaintance extends Component implements AcquaintanceInterface
{
    public const EVENT_BEFORE_BEFRIENDING = 'podium.acquaintance.befriending.before';
    public const EVENT_AFTER_BEFRIENDING = 'podium.acquaintance.befriending.after';
    public const EVENT_BEFORE_UNFRIENDING = 'podium.acquaintance.unfriending.before';
    public const EVENT_AFTER_UNFRIENDING = 'podium.acquaintance.unfriending.after';
    public const EVENT_BEFORE_IGNORING = 'podium.acquaintance.ignoring.before';
    public const EVENT_AFTER_IGNORING = 'podium.acquaintance.ignoring.after';
    public const EVENT_BEFORE_UNIGNORING = 'podium.acquaintance.unignoring.before';
    public const EVENT_AFTER_UNIGNORING = 'podium.acquaintance.unignoring.after';

    public function beforeBefriend(): bool
    {
        $event = new AcquaintanceEvent();
        $this->trigger(self::EVENT_BEFORE_BEFRIENDING, $event);

        return $event->canBeFriends;
    }

    public function befriend(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse {
        if (!$this->beforeBefriend()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$acquaintance->fetchOne($member, $target)) {
                $acquaintance->prepare($member, $target);
            }
            if (!$acquaintance->befriend()) {
                throw new ServiceException($acquaintance->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while befriending member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterBefriend($acquaintance);

        return PodiumResponse::success();
    }

    public function afterBefriend(AcquaintanceRepositoryInterface $acquaintance): void
    {
        $this->trigger(self::EVENT_AFTER_BEFRIENDING, new AcquaintanceEvent(['repository' => $acquaintance]));
    }

    public function beforeUnfriend(): bool
    {
        $event = new AcquaintanceEvent();
        $this->trigger(self::EVENT_BEFORE_UNFRIENDING, $event);

        return $event->canUnfriend;
    }

    public function unfriend(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse {
        if (!$this->beforeUnfriend()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$acquaintance->fetchOne($member, $target)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'acquaintance.not.exists')]);
            }
            if ($acquaintance->isIgnoring()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.ignores.target')]);
            }

            if (!$acquaintance->delete()) {
                throw new ServiceException($acquaintance->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while unfriending member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterUnfriend();

        return PodiumResponse::success();
    }

    public function afterUnfriend(): void
    {
        $this->trigger(self::EVENT_AFTER_UNFRIENDING);
    }

    public function beforeIgnore(): bool
    {
        $event = new AcquaintanceEvent();
        $this->trigger(self::EVENT_BEFORE_IGNORING, $event);

        return $event->canIgnore;
    }

    public function ignore(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse {
        if (!$this->beforeIgnore()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$acquaintance->fetchOne($member, $target)) {
                $acquaintance->prepare($member, $target);
            }
            if (!$acquaintance->ignore()) {
                throw new ServiceException($acquaintance->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while ignoring member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterIgnore($acquaintance);

        return PodiumResponse::success();
    }

    public function afterIgnore(AcquaintanceRepositoryInterface $acquaintance): void
    {
        $this->trigger(self::EVENT_AFTER_IGNORING, new AcquaintanceEvent(['repository' => $acquaintance]));
    }

    public function beforeUnignore(): bool
    {
        $event = new AcquaintanceEvent();
        $this->trigger(self::EVENT_BEFORE_UNIGNORING, $event);

        return $event->canUnfriend;
    }

    public function unignore(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse {
        if (!$this->beforeUnignore()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$acquaintance->fetchOne($member, $target)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'acquaintance.not.exists')]);
            }
            if ($acquaintance->isFriend()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.befriends.target')]);
            }

            if (!$acquaintance->delete()) {
                throw new ServiceException($acquaintance->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while unignoring member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterUnignore();

        return PodiumResponse::success();
    }

    public function afterUnignore(): void
    {
        $this->trigger(self::EVENT_AFTER_UNIGNORING);
    }
}
