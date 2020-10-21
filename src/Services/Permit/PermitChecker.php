<?php

declare(strict_types=1);

namespace Podium\Api\Services\Permit;

use Podium\Api\Events\PermitEvent;
use Podium\Api\Interfaces\CheckerInterface;
use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumDecision;
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
    public function check(
        DeciderInterface $decider,
        string $type,
        RepositoryInterface $subject = null,
        MemberRepositoryInterface $member = null
    ): PodiumDecision {
        if (!$this->beforeCheck()) {
            return PodiumDecision::deny();
        }

        try {
            $decider->setType($type);
            $decider->setSubject($subject);
            $decider->setMember($member);
            $decision = $decider->decide();
        } catch (Throwable $exc) {
            Yii::error(['Exception while checking permit', $exc->getMessage(), $exc->getTraceAsString()], 'podium');

            return PodiumDecision::deny();
        }

        $this->afterCheck();

        return $decision;
    }

    /**
     * Calls after checking the permit successfully.
     */
    private function afterCheck(): void
    {
        $this->trigger(self::EVENT_AFTER_CHECKING);
    }
}
