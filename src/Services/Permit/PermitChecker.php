<?php

declare(strict_types=1);

namespace Podium\Api\Services\Permit;

use Podium\Api\Events\PermitEvent;
use Podium\Api\Interfaces\AllowerInterface;
use Podium\Api\Interfaces\CheckerInterface;
use Throwable;
use Yii;
use yii\base\Component;

final class PermitChecker extends Component implements CheckerInterface
{
    public const EVENT_BEFORE_CHECKING = 'podium.permit.checking.before';
    public const EVENT_AFTER_CHECKING = 'podium.permit.checking.after';

    /**
     * Calls before checking the permit.
     */
    private function beforeCheck(): bool
    {
        $event = new PermitEvent();
        $this->trigger(self::EVENT_BEFORE_CHECKING, $event);

        return $event->canCheck;
    }

    /**
     * Checks the permit.
     */
    public function check(AllowerInterface $allower): bool
    {
        if (!$this->beforeCheck()) {
            return false;
        }

        try {
            $allowed = $allower->isAllowed();
        } catch (Throwable $exc) {
            Yii::error(['Exception while checking permit', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return false;
        }

        $this->afterCheck();

        return $allowed;
    }

    /**
     * Calls after checking the permit successfully.
     */
    private function afterCheck(): void
    {
        $this->trigger(self::EVENT_AFTER_CHECKING);
    }
}
