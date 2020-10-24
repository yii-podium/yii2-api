<?php

declare(strict_types=1);

namespace Podium\Api\Services\Permit;

use Podium\Api\Enums\Decision;
use Podium\Api\Interfaces\CombinedDeciderInterface;
use Podium\Api\Interfaces\DeciderInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\RepositoryInterface;
use Podium\Api\PodiumDecision;
use Yii;

/**
 * AndDecider allows to combine two or more decisions into one decision - when one of Deciders votes "deny"
 * the result is "deny", otherwise it's "allow".
 */
final class AndDecider implements CombinedDeciderInterface
{
    private ?string $type = null;

    private ?RepositoryInterface $subject = null;

    private ?MemberRepositoryInterface $member = null;

    /**
     * @var DeciderInterface[]
     */
    private array $deciders = [];

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function setSubject(?RepositoryInterface $subject): void
    {
        $this->subject = $subject;
    }

    public function setMember(?MemberRepositoryInterface $member): void
    {
        $this->member = $member;
    }

    public function setDeciders(array $deciders): void
    {
        $this->deciders = $deciders;
    }

    public function decide(): PodiumDecision
    {
        foreach ($this->deciders as $decider) {
            if (!$decider instanceof DeciderInterface) {
                $decider = Yii::createObject($decider);
            }

            $decider->setMember($this->member);
            $decider->setSubject($this->subject);
            $decider->setType($this->type);

            if (Decision::DENY === $decider->decide()->getDecision()) {
                return PodiumDecision::deny();
            }
        }

        return PodiumDecision::allow();
    }
}
