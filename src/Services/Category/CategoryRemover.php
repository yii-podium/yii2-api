<?php

declare(strict_types=1);

namespace Podium\Api\Services\Category;

use InvalidArgumentException;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Events\RemoveEvent;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class CategoryRemover extends Component implements RemoverInterface
{
    public const EVENT_BEFORE_REMOVING = 'podium.category.removing.before';
    public const EVENT_AFTER_REMOVING = 'podium.category.removing.after';

    /**
     * Calls before removing the category.
     */
    private function beforeRemove(): bool
    {
        $event = new RemoveEvent();
        $this->trigger(self::EVENT_BEFORE_REMOVING, $event);

        return $event->canRemove;
    }

    /**
     * Removes the category.
     */
    public function remove(RepositoryInterface $category): PodiumResponse
    {
        if (!$category instanceof CategoryRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeRemove()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$category->isArchived()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'category.must.be.archived')]);
            }

            if (!$category->delete()) {
                throw new ServiceException($category->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while deleting category', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterRemove();

        return PodiumResponse::success();
    }

    /**
     * Calls after removing the category.
     */
    private function afterRemove(): void
    {
        $this->trigger(self::EVENT_AFTER_REMOVING);
    }
}
