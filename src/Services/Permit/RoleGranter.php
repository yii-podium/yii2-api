<?php

declare(strict_types=1);

namespace Podium\Api\Services\Permit;

use Podium\Api\Events\GrantEvent;
use Podium\Api\Interfaces\GranterInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class RoleGranter extends Component implements GranterInterface
{
    public const EVENT_BEFORE_GRANTING = 'podium.role.granting.before';
    public const EVENT_AFTER_GRANTING = 'podium.role.granting.after';
    public const EVENT_BEFORE_REVOKING = 'podium.role.revoking.before';
    public const EVENT_AFTER_REVOKING = 'podium.role.revoking.after';

    /**
     * Calls before granting the role.
     */
    private function beforeGrant(): bool
    {
        $event = new GrantEvent();
        $this->trigger(self::EVENT_BEFORE_GRANTING, $event);

        return $event->canGrant;
    }

    /**
     * Grants the role to the member.
     */
    public function grant(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse
    {
        if (!$this->beforeGrant()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($member->hasRole($role)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'role.already.granted')]);
            }

            if (!$member->addRole($role)) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while granting role', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterGrant($member);

        return PodiumResponse::success();
    }

    /**
     * Calls after granting the role successfully.
     */
    private function afterGrant(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_GRANTING, new GrantEvent(['repository' => $member]));
    }

    /**
     * Calls before revoking the role.
     */
    private function beforeRevoke(): bool
    {
        $event = new GrantEvent();
        $this->trigger(self::EVENT_BEFORE_REVOKING, $event);

        return $event->canRevoke;
    }

    /**
     * Revokes the role from the member.
     */
    public function revoke(MemberRepositoryInterface $member, RoleRepositoryInterface $role): PodiumResponse
    {
        if (!$this->beforeRevoke()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$member->hasRole($role)) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'role.not.granted')]);
            }

            if (!$member->removeRole($role)) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while revoking role', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRevoke($member);

        return PodiumResponse::success();
    }

    /**
     * Calls after revoking the role successfully.
     */
    private function afterRevoke(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_REVOKING, new GrantEvent(['repository' => $member]));
    }
}
