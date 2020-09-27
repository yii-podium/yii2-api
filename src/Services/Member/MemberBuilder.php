<?php

declare(strict_types=1);

namespace Podium\Api\Services\Member;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\MemberBuilderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
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
    public const EVENT_BEFORE_ACTIVATING = 'podium.member.activating.before';
    public const EVENT_AFTER_ACTIVATING = 'podium.member.activating.after';

    public function beforeRegister(): bool
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

            return PodiumResponse::error();
        }

        $this->afterRegister($member);

        return PodiumResponse::success();
    }

    public function afterRegister(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_REGISTERING, new BuildEvent(['repository' => $member]));
    }

    public function beforeEdit(): bool
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

            return PodiumResponse::error();
        }

        $this->afterEdit($member);

        return PodiumResponse::success();
    }

    public function afterEdit(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $member]));
    }

    public function beforeActivate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_ACTIVATING, $event);

        return $event->canEdit;
    }

    /**
     * Activates the member.
     */
    public function activate(MemberRepositoryInterface $member): PodiumResponse
    {
        if (!$this->beforeActivate()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$member->activate()) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while activating member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error();
        }

        $this->afterActivate($member);

        return PodiumResponse::success();
    }

    public function afterActivate(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_ACTIVATING, new BuildEvent(['repository' => $member]));
    }
}
