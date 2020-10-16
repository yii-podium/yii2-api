<?php

declare(strict_types=1);

namespace Podium\Api\Interfaces;

use Podium\Api\PodiumResponse;

interface MemberInterface
{
    /**
     * Returns the member repository.
     */
    public function getMemberRepository(): MemberRepositoryInterface;

    /**
     * Returns the acquaintance repository.
     */
    public function getAcquaintanceRepository(): AcquaintanceRepositoryInterface;

    /**
     * Registers the member.
     *
     * @param int|string|array $id
     */
    public function register($id, array $data = []): PodiumResponse;

    /**
     * Removes the member.
     */
    public function remove(MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Edits the member.
     */
    public function edit(MemberRepositoryInterface $member, array $data = []): PodiumResponse;

    /**
     * Befriends the target as the member.
     */
    public function befriend(MemberRepositoryInterface $member, MemberRepositoryInterface $target): PodiumResponse;

    /**
     * Ignores the target as the member.
     */
    public function ignore(MemberRepositoryInterface $member, MemberRepositoryInterface $target): PodiumResponse;

    /**
     * Disconnects the target from the member.
     */
    public function disconnect(MemberRepositoryInterface $member, MemberRepositoryInterface $target): PodiumResponse;

    /**
     * Bans the member.
     */
    public function ban(MemberRepositoryInterface $member): PodiumResponse;

    /**
     * Unbans the member.
     */
    public function unban(MemberRepositoryInterface $member): PodiumResponse;
}
