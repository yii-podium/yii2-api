<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Thread;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Thread;
use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\BookmarkerInterface;
use Podium\Api\Interfaces\BookmarkRepositoryInterface;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\ForumRepositoryInterface;
use Podium\Api\Interfaces\LockerInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\PinnerInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\SubscriberInterface;
use Podium\Api\Interfaces\SubscriptionRepositoryInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\PodiumResponse;
use yii\base\InvalidConfigException;

class ThreadComponentTest extends TestCase
{
    private Thread $component;

    protected function setUp(): void
    {
        $this->component = new Thread();
    }

    public function testGetBuilderShouldThrowExceptionWhenBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->builderConfig = '';

        $this->component->getBuilder();
    }

    public function testCreateShouldRunBuildersCreate(): void
    {
        $builder = $this->createMock(CategorisedBuilderInterface::class);
        $builder->expects(self::once())->method('create')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;
        $this->component->threadRepositoryConfig = $this->createMock(ThreadRepositoryInterface::class);

        $this->component->create(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(ForumRepositoryInterface::class)
        );
    }

    public function testEditShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(CategorisedBuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->edit($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testGetRemoverShouldThrowExceptionWhenRemoverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->removerConfig = '';

        $this->component->getRemover();
    }

    public function testRemoveShouldRunRemoversRemove(): void
    {
        $remover = $this->createMock(RemoverInterface::class);
        $remover->expects(self::once())->method('remove')->willReturn(PodiumResponse::success());
        $this->component->removerConfig = $remover;

        $this->component->remove($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testGetArchiverShouldThrowExceptionWhenArchiverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->archiverConfig = '';

        $this->component->getArchiver();
    }

    public function testArchiveShouldRunArchiversArchive(): void
    {
        $archiver = $this->createMock(ArchiverInterface::class);
        $archiver->expects(self::once())->method('archive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->archive($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testReviveShouldRunArchiversRevive(): void
    {
        $archiver = $this->createMock(ArchiverInterface::class);
        $archiver->expects(self::once())->method('revive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->revive($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testGetMoverShouldThrowExceptionWhenMoverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->moverConfig = '';

        $this->component->getMover();
    }

    public function testMoveShouldRunMoversMove(): void
    {
        $mover = $this->createMock(MoverInterface::class);
        $mover->expects(self::once())->method('move')->willReturn(PodiumResponse::success());
        $this->component->moverConfig = $mover;

        $this->component->move(
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(ForumRepositoryInterface::class)
        );
    }

    public function testGetPinnerShouldThrowExceptionWhenPinnerIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->pinnerConfig = '';

        $this->component->getPinner();
    }

    public function testPinShouldRunPinnersPin(): void
    {
        $pinner = $this->createMock(PinnerInterface::class);
        $pinner->expects(self::once())->method('pin')->willReturn(PodiumResponse::success());
        $this->component->pinnerConfig = $pinner;

        $this->component->pin($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testUnpinShouldRunPinnersUnpin(): void
    {
        $pinner = $this->createMock(PinnerInterface::class);
        $pinner->expects(self::once())->method('unpin')->willReturn(PodiumResponse::success());
        $this->component->pinnerConfig = $pinner;

        $this->component->unpin($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testGetLockerShouldThrowExceptionWhenLockerIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->lockerConfig = '';

        $this->component->getLocker();
    }

    public function testLockShouldRunLockersLock(): void
    {
        $locker = $this->createMock(LockerInterface::class);
        $locker->expects(self::once())->method('lock')->willReturn(PodiumResponse::success());
        $this->component->lockerConfig = $locker;

        $this->component->lock($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testUnlockShouldRunLockersUnlock(): void
    {
        $locker = $this->createMock(LockerInterface::class);
        $locker->expects(self::once())->method('unlock')->willReturn(PodiumResponse::success());
        $this->component->lockerConfig = $locker;

        $this->component->unlock($this->createMock(ThreadRepositoryInterface::class));
    }

    public function testGetSubscriberShouldThrowExceptionWhenSubscriberIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->subscriberConfig = '';

        $this->component->getSubscriber();
    }

    public function testSubscribeShouldRunSubscriberSubscribe(): void
    {
        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects(self::once())->method('subscribe')->willReturn(PodiumResponse::success());
        $this->component->subscriberConfig = $subscriber;
        $this->component->subscriptionRepositoryConfig = $this->createMock(SubscriptionRepositoryInterface::class);

        $this->component->subscribe(
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testUnsubscribeShouldRunSubscriberUnsubscribe(): void
    {
        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects(self::once())->method('unsubscribe')->willReturn(PodiumResponse::success());
        $this->component->subscriberConfig = $subscriber;
        $this->component->subscriptionRepositoryConfig = $this->createMock(SubscriptionRepositoryInterface::class);

        $this->component->unsubscribe(
            $this->createMock(ThreadRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testGetBookmarkerShouldThrowExceptionWhenBookmarkerIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->bookmarkerConfig = '';

        $this->component->getBookmarker();
    }

    public function testMarkShouldRunBookmarkerMark(): void
    {
        $bookmarker = $this->createMock(BookmarkerInterface::class);
        $bookmarker->expects(self::once())->method('mark')->willReturn(PodiumResponse::success());
        $this->component->bookmarkerConfig = $bookmarker;
        $this->component->bookmarkRepositoryConfig = $this->createMock(BookmarkRepositoryInterface::class);

        $this->component->mark(
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testGetRepositoryShouldThrowExceptionWhenRepositoryIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->threadRepositoryConfig = '';

        $this->component->getThreadRepository();
    }

    public function testGetSubscriptionRepositoryShouldThrowExceptionWhenSubscriptionRepositoryIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->subscriptionRepositoryConfig = '';

        $this->component->getSubscriptionRepository();
    }

    public function testGetBookmarkRepositoryShouldThrowExceptionWhenBookmarkRepositoryIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->bookmarkRepositoryConfig = '';

        $this->component->getBookmarkRepository();
    }
}
