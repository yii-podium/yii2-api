<?php

declare(strict_types=1);

namespace Podium\Api\Services\Member;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BanEvent;
use Podium\Api\Interfaces\BanisherInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class MemberBanisher extends Component implements BanisherInterface
{
    public const EVENT_BEFORE_BANNING = 'podium.banning.before';
    public const EVENT_AFTER_BANNING = 'podium.banning.after';
    public const EVENT_BEFORE_UNBANNING = 'podium.unbanning.before';
    public const EVENT_AFTER_UNBANNING = 'podium.unbanning.after';

    /**
     * Calls before banning the member.
     */
    private function beforeBan(): bool
    {
        $event = new BanEvent();
        $this->trigger(self::EVENT_BEFORE_BANNING, $event);

        return $event->canBan;
    }

    /**
     * Bans the member.
     */
    public function ban(MemberRepositoryInterface $member): PodiumResponse
    {
        if (!$this->beforeBan()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($member->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.already.banned')]);
            }

            if (!$member->ban()) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while banning member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterBan($member);

        return PodiumResponse::success();
    }

    /**
     * Calls after banning the member successfully.
     */
    private function afterBan(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_BANNING, new BanEvent(['repository' => $member]));
    }

    /**
     * Calls before unbanning the member.
     */
    private function beforeUnban(): bool
    {
        $event = new BanEvent();
        $this->trigger(self::EVENT_BEFORE_UNBANNING, $event);

        return $event->canUnban;
    }

    /**
     * Unbans the member.
     */
    public function unban(MemberRepositoryInterface $member): PodiumResponse
    {
        if (!$this->beforeUnban()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$member->isBanned()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'member.not.banned')]);
            }

            if (!$member->unban()) {
                throw new ServiceException($member->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while unbanning member', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterUnban($member);

        return PodiumResponse::success();
    }

    /**
     * Calls after unbanning the member successfully.
     */
    private function afterUnban(MemberRepositoryInterface $member): void
    {
        $this->trigger(self::EVENT_AFTER_UNBANNING, new BanEvent(['repository' => $member]));
    }
}
