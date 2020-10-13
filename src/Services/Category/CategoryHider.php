<?php

declare(strict_types=1);

namespace Podium\Api\Services\Category;

use InvalidArgumentException;
use Podium\Api\Events\HideEvent;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\HiderInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumResponse;
use Podium\Api\Services\ServiceException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\db\Transaction;

final class CategoryHider extends Component implements HiderInterface
{
    public const EVENT_BEFORE_HIDING = 'podium.category.hiding.before';
    public const EVENT_AFTER_HIDING = 'podium.category.hiding.after';
    public const EVENT_BEFORE_REVEALING = 'podium.category.revealing.before';
    public const EVENT_AFTER_REVEALING = 'podium.category.revealing.after';

    /**
     * Calls before hiding the category.
     */
    private function beforeHide(): bool
    {
        $event = new HideEvent();
        $this->trigger(self::EVENT_BEFORE_HIDING, $event);

        return $event->canHide;
    }

    /**
     * Hides the category.
     */
    public function hide(RepositoryInterface $category): PodiumResponse
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

        if (!$this->beforeHide()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($category->isHidden()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'category.already.hidden')]);
            }

            if (!$category->hide()) {
                throw new ServiceException($category->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while hiding category', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterHide($category);

        return PodiumResponse::success();
    }

    /**
     * Calls after successful hiding the category.
     */
    private function afterHide(CategoryRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_HIDING, new HideEvent(['repository' => $category]));
    }

    /**
     * Calls before revealing the category.
     */
    private function beforeReveal(): bool
    {
        $event = new HideEvent();
        $this->trigger(self::EVENT_BEFORE_REVEALING, $event);

        return $event->canReveal;
    }

    /**
     * Reveal the category.
     */
    public function reveal(RepositoryInterface $category): PodiumResponse
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

        if (!$this->beforeReveal()) {
            return PodiumResponse::error();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$category->isHidden()) {
                throw new ServiceException(['api' => Yii::t('podium.error', 'category.not.hidden')]);
            }

            if (!$category->reveal()) {
                throw new ServiceException($category->getErrors());
            }

            $transaction->commit();
        } catch (ServiceException $exc) {
            $transaction->rollBack();

            return PodiumResponse::error($exc->getErrorList());
        } catch (Throwable $exc) {
            $transaction->rollBack();
            Yii::error(['Exception while revealing category', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumResponse::error(['exception' => $exc]);
        }

        $this->afterReveal($category);

        return PodiumResponse::success();
    }

    /**
     * Calls after successful revealing the category.
     */
    private function afterReveal(CategoryRepositoryInterface $category): void
    {
        $this->trigger(self::EVENT_AFTER_REVEALING, new HideEvent(['repository' => $category]));
    }
}
