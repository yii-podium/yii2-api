<?php

declare(strict_types=1);

namespace Podium\Api\Services\Category;

use InvalidArgumentException;
use Podium\Api\Events\SortEvent;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\Interfaces\SorterInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Exception;
use yii\db\Transaction;

final class CategorySorter extends Component implements SorterInterface
{
    public const EVENT_BEFORE_REPLACING = 'podium.category.replacing.before';
    public const EVENT_AFTER_REPLACING = 'podium.category.replacing.after';
    public const EVENT_BEFORE_SORTING = 'podium.category.sorting.before';
    public const EVENT_AFTER_SORTING = 'podium.category.sorting.after';

    /**
     * Calls before replacing the order of categories.
     */
    private function beforeReplace(): bool
    {
        $event = new SortEvent();
        $this->trigger(self::EVENT_BEFORE_REPLACING, $event);

        return $event->canReplace;
    }

    /**
     * Replaces the spot of the categories.
     */
    public function replace(RepositoryInterface $firstCategory, RepositoryInterface $secondCategory): PodiumResponse
    {
        if (!$firstCategory instanceof CategoryRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'First category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$secondCategory instanceof CategoryRepositoryInterface) {
            return PodiumResponse::error(
                [
                    'exception' => new InvalidArgumentException(
                        'Second category must be instance of Podium\Api\Interfaces\CategoryRepositoryInterface!'
                    ),
                ]
            );
        }

        if (!$this->beforeReplace()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldOrder = $firstCategory->getOrder();
            if (!$firstCategory->setOrder($secondCategory->getOrder())) {
                throw new Exception('Error while setting new category order!');
            }
            if (!$secondCategory->setOrder($oldOrder)) {
                throw new Exception('Error while setting new category order!');
            }

            $transaction->commit();
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(
                ['Exception while replacing categories order', $exc->getMessage(), $exc->getTraceAsString()],
                'podium'
            );

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterReplace();

        return PodiumResponse::success();
    }

    /**
     * Calls after successful replacing the order of categories.
     */
    private function afterReplace(): void
    {
        $this->trigger(self::EVENT_AFTER_REPLACING);
    }

    /**
     * Calls before sorting categories.
     */
    private function beforeSort(): bool
    {
        $event = new SortEvent();
        $this->trigger(self::EVENT_BEFORE_SORTING, $event);

        return $event->canSort;
    }

    /**
     * Sorts the categories.
     */
    public function sort(RepositoryInterface $category): PodiumResponse
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

        if (!$this->beforeSort()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$category->sort()) {
                throw new ServiceException($category->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(
                ['Exception while sorting categories', $exc->getMessage(), $exc->getTraceAsString()],
                'podium'
            );

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterSort();

        return PodiumResponse::success();
    }

    /**
     * Calls after successful sorting of categories.
     */
    private function afterSort(): void
    {
        $this->trigger(self::EVENT_AFTER_SORTING);
    }
}
