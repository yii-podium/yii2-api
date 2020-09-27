<?php

declare(strict_types=1);

namespace Podium\Api\Services\Rank;

use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\RankRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class RankBuilder extends Component implements BuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.rank.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.rank.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.rank.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.rank.editing.after';

    /**
     * Calls before creating a rank.
     */
    public function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new rank.
     */
    public function create(RepositoryInterface $rank, array $data = []): PodiumResponse
    {
        if (!$rank instanceof RankRepositoryInterface || !$this->beforeCreate()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$rank->create($data)) {
                throw new ServiceException($rank->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while creating rank', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterCreate($rank);

        return PodiumResponse::success();
    }

    /**
     * Calls after creating the rank successfully.
     */
    public function afterCreate(RankRepositoryInterface $rank): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $rank]));
    }

    /**
     * Calls before editing the rank.
     */
    public function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the rank.
     */
    public function edit(RepositoryInterface $rank, array $data = []): PodiumResponse
    {
        if (!$rank instanceof RankRepositoryInterface || !$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$rank->edit($data)) {
                throw new ServiceException($rank->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while editing rank', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterEdit($rank);

        return PodiumResponse::success();
    }

    /**
     * Calls after editing the rank successfully.
     */
    public function afterEdit(RankRepositoryInterface $rank): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $rank]));
    }
}
