<?php

declare(strict_types=1);

namespace Podium\Api\Services\Member;

use Podium\Api\Events\AcquaintanceEvent;
use Podium\Api\Interfaces\AcquaintanceInterface;
use Podium\Api\Interfaces\AcquaintanceRepositoryInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MemberAcquaintance extends Component implements AcquaintanceInterface
{
    public const EVENT_BEFORE_BEFRIENDING = 'podium.acquaintance.befriending.before';
    public const EVENT_AFTER_BEFRIENDING = 'podium.acquaintance.befriending.after';
    public const EVENT_BEFORE_IGNORING = 'podium.acquaintance.ignoring.before';
    public const EVENT_AFTER_IGNORING = 'podium.acquaintance.ignoring.after';
    public const EVENT_BEFORE_DISCONNECTING = 'podium.acquaintance.disconnecting.before';
    public const EVENT_AFTER_DISCONNECTING = 'podium.acquaintance.disconnecting.after';

    /**
     * Calls before befriending the member.
     */
    private function beforeBefriend(): bool
    {
        $event = new AcquaintanceEvent();
        $this->trigger(self::EVENT_BEFORE_BEFRIENDING, $event);

        return $event->canBeFriends;
    }

    /**
     * Befriends the member.
     */
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
            if ($member->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if ($target->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if ($member->getId() === $target->getId()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'target.is.member')]);
            }

            if (!$acquaintance->fetchOne($member, $target)) {
                $acquaintance->prepare($member, $target);
            }

            if ($acquaintance->isFriend()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'target.already.friend')]);
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

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterBefriend($acquaintance);

        return PodiumResponse::success();
    }

    /**
     * Calls after befriending the member successfully.
     */
    private function afterBefriend(AcquaintanceRepositoryInterface $acquaintance): void
    {
        $this->trigger(self::EVENT_AFTER_BEFRIENDING, new AcquaintanceEvent(['repository' => $acquaintance]));
    }

    /**
     * Calls before ignoring the member.
     */
    private function beforeIgnore(): bool
    {
        $event = new AcquaintanceEvent();
        $this->trigger(self::EVENT_BEFORE_IGNORING, $event);

        return $event->canIgnore;
    }

    /**
     * Ignores the member.
     */
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
            if ($member->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if ($target->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if ($member->getId() === $target->getId()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'target.is.member')]);
            }

            if (!$acquaintance->fetchOne($member, $target)) {
                $acquaintance->prepare($member, $target);
            }

            if ($acquaintance->isIgnoring()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'target.already.ignored')]);
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

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterIgnore($acquaintance);

        return PodiumResponse::success();
    }

    /**
     * Calls after ignoring the member successfully.
     */
    private function afterIgnore(AcquaintanceRepositoryInterface $acquaintance): void
    {
        $this->trigger(self::EVENT_AFTER_IGNORING, new AcquaintanceEvent(['repository' => $acquaintance]));
    }

    /**
     * Calls before disconnecting the member.
     */
    private function beforeDisconnect(): bool
    {
        $event = new AcquaintanceEvent();
        $this->trigger(self::EVENT_BEFORE_DISCONNECTING, $event);

        return $event->canDisconnect;
    }

    /**
     * Disconnects the member.
     */
    public function disconnect(
        AcquaintanceRepositoryInterface $acquaintance,
        MemberRepositoryInterface $member,
        MemberRepositoryInterface $target
    ): PodiumResponse {
        if (!$this->beforeDisconnect()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($member->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if ($target->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if ($member->getId() === $target->getId()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'target.is.member')]);
            }

            if (!$acquaintance->fetchOne($member, $target)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'acquaintance.not.exists')]);
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
            Yii::error(
                ['Exception while disconnecting member', $exc->getMessage(), $exc->getTraceAsString()],
                'podium'
            );

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterDisconnect();

        return PodiumResponse::success();
    }

    /**
     * Calls after disconnecting the member successfully.
     */
    private function afterDisconnect(): void
    {
        $this->trigger(self::EVENT_AFTER_DISCONNECTING);
    }
}
