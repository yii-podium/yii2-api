<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Account;

use DomainException;
use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Account;
use Podium\Api\Components\NoMembershipException;
use Podium\Api\Interfaces\CategoryInterface;
use Podium\Api\Interfaces\CategoryRepositoryInterface;
use Podium\Api\Interfaces\ForumInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\GroupInterface;
use Podium\Api\Interfaces\GroupRepositoryInterface;
use Podium\Api\Interfaces\LoggerInterface;
use Podium\Api\Interfaces\MemberInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Interfaces\PollPostInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PostInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\ThreadInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Module;
use Podium\Api\PodiumResponse;
use yii\base\InvalidConfigException;
use yii\web\User;

class AccountComponentTest extends TestCase
{
    private Account $component;

    protected function setUp(): void
    {
        $this->component = new Account();

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $this->component->userConfig = $user;

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->method('fetchOne')->willReturn(true);
        $this->component->repositoryConfig = $member;
    }

    public function testGetPodiumShouldThrowExceptionWhenPodiumIsNotSet(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->getPodium();
    }

    public function testGetMembershipShouldReturnLoadedMemberRepository(): void
    {
        $user = $this->createMock(User::class);
        $user->expects(self::once())->method('getId')->willReturn(1);
        $this->component->userConfig = $user;

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->expects(self::once())->method('fetchOne')->with(['user_id' => '1'])->willReturn(true);
        $this->component->repositoryConfig = $member;

        $this->component->getMembership();
        $this->component->getMembership(); // second one to test internal cache
    }

    public function testGetMembershipShouldReloadMemberIfRequested(): void
    {
        $user = $this->createMock(User::class);
        $user->expects(self::exactly(2))->method('getId')->willReturn(1);
        $this->component->userConfig = $user;

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->expects(self::exactly(2))->method('fetchOne')->willReturn(true);
        $this->component->repositoryConfig = $member;

        $this->component->getMembership();
        $this->component->getMembership(true);
    }

    public function testGetMembershipShouldThrowExceptionWhenUserComponentIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->userConfig = '';

        $this->component->getMembership();
    }

    public function testGetMembershipShouldThrowExceptionWhenUserJsonedIdIsNotString(): void
    {
        $this->expectException(DomainException::class);

        $user = $this->createMock(User::class);
        $user->expects(self::once())->method('getId')->willReturn(null);
        $this->component->userConfig = $user;

        $this->component->getMembership();
    }

    public function testGetMembershipShouldThrowExceptionWhenMemberRepositoryIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $this->component->userConfig = $user;

        $this->component->repositoryConfig = '';

        $this->component->getMembership();
    }

    public function testGetMembershipShouldThrowExceptionWhenMembershipIsNotFound(): void
    {
        $this->expectException(NoMembershipException::class);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $this->component->userConfig = $user;

        $member = $this->createMock(MemberRepositoryInterface::class);
        $member->expects(self::once())->method('fetchOne')->willReturn(false);
        $this->component->repositoryConfig = $member;

        $this->component->getMembership();
    }

    public function testJoinGroupShouldCallGroupComponentJoin(): void
    {
        $group = $this->createMock(GroupInterface::class);
        $group->expects(self::once())->method('join')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getGroup')->willReturn($group);
        $this->component->setPodium($podium);

        $this->component->joinGroup($this->createMock(GroupRepositoryInterface::class));
    }

    public function testLeaveGroupShouldCallGroupComponentLeave(): void
    {
        $group = $this->createMock(GroupInterface::class);
        $group->expects(self::once())->method('leave')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getGroup')->willReturn($group);
        $this->component->setPodium($podium);

        $this->component->leaveGroup($this->createMock(GroupRepositoryInterface::class));
    }

    public function testCreateCategoryShouldCallCategoryComponentCreate(): void
    {
        $category = $this->createMock(CategoryInterface::class);
        $category->expects(self::once())->method('create')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getCategory')->willReturn($category);
        $this->component->setPodium($podium);

        $this->component->createCategory();
    }

    public function testCreateForumShouldCallForumComponentCreate(): void
    {
        $forum = $this->createMock(ForumInterface::class);
        $forum->expects(self::once())->method('create')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getForum')->willReturn($forum);
        $this->component->setPodium($podium);

        $this->component->createForum($this->createMock(CategoryRepositoryInterface::class));
    }

    public function testCreateThreadShouldCallThreadComponentCreate(): void
    {
        $thread = $this->createMock(ThreadInterface::class);
        $thread->expects(self::once())->method('create')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getThread')->willReturn($thread);
        $this->component->setPodium($podium);

        $this->component->createThread($this->createMock(ForumRepositoryInterface::class));
    }

    public function testCreatePostShouldCallPostComponentCreate(): void
    {
        $post = $this->createMock(PostInterface::class);
        $post->expects(self::once())->method('create')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getPost')->willReturn($post);
        $this->component->setPodium($podium);

        $this->component->createPost($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testMarkThreadShouldCallThreadComponentMark(): void
    {
        $thread = $this->createMock(ThreadInterface::class);
        $thread->expects(self::once())->method('mark')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getThread')->willReturn($thread);
        $this->component->setPodium($podium);

        $this->component->markThread($this->createMock(PostRepositoryInterface::class));
    }

    public function testSubscribeThreadShouldCallThreadComponentSubscribe(): void
    {
        $thread = $this->createMock(ThreadInterface::class);
        $thread->expects(self::once())->method('subscribe')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getThread')->willReturn($thread);
        $this->component->setPodium($podium);

        $this->component->subscribeThread($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testUnsubscribeThreadShouldCallThreadComponentUnsubscribe(): void
    {
        $thread = $this->createMock(ThreadInterface::class);
        $thread->expects(self::once())->method('unsubscribe')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getThread')->willReturn($thread);
        $this->component->setPodium($podium);

        $this->component->unsubscribeThread($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testThumbUpPostShouldCallPostComponentThumbUp(): void
    {
        $post = $this->createMock(PostInterface::class);
        $post->expects(self::once())->method('thumbUp')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getPost')->willReturn($post);
        $this->component->setPodium($podium);

        $this->component->thumbUpPost($this->createMock(PostRepositoryInterface::class));
    }

    public function testThumbDownPostShouldCallPostComponentThumbDown(): void
    {
        $post = $this->createMock(PostInterface::class);
        $post->expects(self::once())->method('thumbDown')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getPost')->willReturn($post);
        $this->component->setPodium($podium);

        $this->component->thumbDownPost($this->createMock(PostRepositoryInterface::class));
    }

    public function testThumbResetPostShouldCallPostComponentThumbReset(): void
    {
        $post = $this->createMock(PostInterface::class);
        $post->expects(self::once())->method('thumbReset')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getPost')->willReturn($post);
        $this->component->setPodium($podium);

        $this->component->thumbResetPost($this->createMock(PostRepositoryInterface::class));
    }

    public function testVotePollShouldCallPostComponentVotePoll(): void
    {
        $post = $this->createMock(PollPostInterface::class);
        $post->expects(self::once())->method('votePoll')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getPost')->willReturn($post);
        $this->component->setPodium($podium);

        $this->component->votePoll($this->createMock(PollPostRepositoryInterface::class), []);
    }

    public function testEditShouldCallMemberComponentEdit(): void
    {
        $member = $this->createMock(MemberInterface::class);
        $member->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMember')->willReturn($member);
        $this->component->setPodium($podium);

        $this->component->edit();
    }

    public function testBefriendMemberShouldCallMemberComponentBefriend(): void
    {
        $member = $this->createMock(MemberInterface::class);
        $member->expects(self::once())->method('befriend')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMember')->willReturn($member);
        $this->component->setPodium($podium);

        $this->component->befriendMember($this->createMock(MemberRepositoryInterface::class));
    }

    public function testUnfriendMemberShouldCallMemberComponentUnfriend(): void
    {
        $member = $this->createMock(MemberInterface::class);
        $member->expects(self::once())->method('unfriend')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMember')->willReturn($member);
        $this->component->setPodium($podium);

        $this->component->unfriendMember($this->createMock(MemberRepositoryInterface::class));
    }

    public function testIgnoreMemberShouldCallMemberComponentIgnore(): void
    {
        $member = $this->createMock(MemberInterface::class);
        $member->expects(self::once())->method('ignore')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMember')->willReturn($member);
        $this->component->setPodium($podium);

        $this->component->ignoreMember($this->createMock(MemberRepositoryInterface::class));
    }

    public function testUnignoreMemberShouldCallMemberComponentUnignore(): void
    {
        $member = $this->createMock(MemberInterface::class);
        $member->expects(self::once())->method('unignore')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMember')->willReturn($member);
        $this->component->setPodium($podium);

        $this->component->unignoreMember($this->createMock(MemberRepositoryInterface::class));
    }

    public function testSendMessageShouldCallMessageComponentSend(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects(self::once())->method('send')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMessage')->willReturn($message);
        $this->component->setPodium($podium);

        $this->component->sendMessage($this->createMock(MemberRepositoryInterface::class));
    }

    public function testRemoveMessageShouldCallMessageComponentRemove(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects(self::once())->method('remove')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMessage')->willReturn($message);
        $this->component->setPodium($podium);

        $this->component->removeMessage($this->createMock(MessageRepositoryInterface::class));
    }

    public function testArchiveMessageShouldCallMessageComponentArchive(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects(self::once())->method('archive')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMessage')->willReturn($message);
        $this->component->setPodium($podium);

        $this->component->archiveMessage($this->createMock(MessageRepositoryInterface::class));
    }

    public function testReviveMessageShouldCallMessageComponentRevive(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->expects(self::once())->method('revive')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getMessage')->willReturn($message);
        $this->component->setPodium($podium);

        $this->component->reviveMessage($this->createMock(MessageRepositoryInterface::class));
    }

    public function testLogShouldCallLoggerComponentCreate(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('create')->willReturn(PodiumResponse::success());

        $podium = $this->createMock(Module::class);
        $podium->expects(self::once())->method('getLogger')->willReturn($logger);
        $this->component->setPodium($podium);

        $this->component->log('action');
    }
}
