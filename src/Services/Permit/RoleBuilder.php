<?php

declare(strict_types=1);

namespace Podium\Api\Services\Permit;

use InvalidArgumentException;
use Podium\Api\Events\BuildEvent;
use Podium\Api\Interfaces\BuilderInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\RoleRepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class RoleBuilder extends Component implements BuilderInterface
{
    public const EVENT_BEFORE_CREATING = 'podium.role.creating.before';
    public const EVENT_AFTER_CREATING = 'podium.role.creating.after';
    public const EVENT_BEFORE_EDITING = 'podium.role.editing.before';
    public const EVENT_AFTER_EDITING = 'podium.role.editing.after';

    /**
     * Calls before creating a role.
     */
    private function beforeCreate(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_CREATING, $event);

        return $event->canCreate;
    }

    /**
     * Creates new role.
     */
    public function create(RepositoryInterface $role, array $data = []): PodiumResponse
    {
        if (!$role instanceof RoleRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Role must be instance of Podium\Api\Interfaces\RoleRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeCreate()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$role->create($data)) {
                throw new ServiceException($role->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while creating role', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterCreate($role);

        return PodiumResponse::success();
    }

    /**
     * Calls after creating the role successfully.
     */
    private function afterCreate(RoleRepositoryInterface $role): void
    {
        $this->trigger(self::EVENT_AFTER_CREATING, new BuildEvent(['repository' => $role]));
    }

    /**
     * Calls before editing the role.
     */
    private function beforeEdit(): bool
    {
        $event = new BuildEvent();
        $this->trigger(self::EVENT_BEFORE_EDITING, $event);

        return $event->canEdit;
    }

    /**
     * Edits the role.
     */
    public function edit(RepositoryInterface $role, array $data = []): PodiumResponse
    {
        if (!$role instanceof RoleRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Role must be instance of Podium\Api\Interfaces\RoleRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeEdit()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$role->edit($data)) {
                throw new ServiceException($role->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while editing role', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterEdit($role);

        return PodiumResponse::success();
    }

    /**
     * Calls after editing the role successfully.
     */
    private function afterEdit(RoleRepositoryInterface $role): void
    {
        $this->trigger(self::EVENT_AFTER_EDITING, new BuildEvent(['repository' => $role]));
    }
}
