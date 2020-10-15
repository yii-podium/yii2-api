<?php

declare(strict_types=1);

namespace Podium\Api\Services\Member;

use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\MemberBuilderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MemberBuilder extends Component implements MemberBuilderInterface
{
    public const EVENT_BEFORE_REGISTERING = 'podium.member.registering.before';
    public const EVENT_AFTER_REGISTERING = 'podium.member.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.member.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.member.editing.after';

    /**
     * Calls before registering a member.
     */
    private function beforeRegister(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_REGISTERING, $event);

        return $event->canCreate;
    }

    /**
     * Registers a member.
     *
     * @param int|string|array $id
     */
    public function register(MemberRepositoryInterface $member, $id, array $data = []): PodiumResponse
    {
        if (!$this->beforeRegister()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$member->register($id, $data)) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while registering member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRegister($member);

        return PodiumResponse::success();
    }

    /**
     * Calls after registering the member successfully.
     */
    private function afterRegister(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_REGISTERING, new BuildEvent(['repository' => $member]));
    }

    /**
     * Calls before editing the member.
     */
    private function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the member.
     */
    public function edit(MemberRepositoryInterface $member, array $data = []): PodiumResponse
    {
        if (!$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($member->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.banned')]);
            }

            if (!$member->edit($data)) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while editing member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterEdit($member);

        return PodiumResponse::success();
    }

    /**
     * Calls after editing the member successfully.
     */
    private function afterEdit(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $member]));
    }
}
