<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Post;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Components\Post;
use Podium\Api\Interfaces\ArchiverInterface;
use Podium\Api\Interfaces\CategorisedBuilderInterface;
use Podium\Api\Interfaces\LikerInterface;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MoverInterface;
use Podium\Api\Interfaces\PinnerInterface;
use Podium\Api\Interfaces\PollBuilderInterface;
use Podium\Api\Interfaces\PollPostRepositoryInterface;
use Podium\Api\Interfaces\PostRepositoryInterface;
use Podium\Api\Interfaces\RemoverInterface;
use Podium\Api\Interfaces\ThreadRepositoryInterface;
use Podium\Api\Interfaces\ThumbRepositoryInterface;
use Podium\Api\Interfaces\VoterInterface;
use yii\base\InvalidConfigException;

class PostComponentTest extends TestCase
{
    private Post $component;

    protected function setUp(): void
    {
        $this->component = new Post();
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
        $this->component->postRepositoryConfig = $this->createMock(PostRepositoryInterface::class);

        $this->component->create(
            $this->createMock(MemberRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class)
        );
    }

    public function testEditShouldRunBuildersEdit(): void
    {
        $builder = $this->createMock(CategorisedBuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->builderConfig = $builder;

        $this->component->edit($this->createMock(PostRepositoryInterface::class));
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

        $this->component->remove($this->createMock(PostRepositoryInterface::class));
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

        $this->component->archive($this->createMock(PostRepositoryInterface::class));
    }

    public function testReviveShouldRunArchiversRevive(): void
    {
        $archiver = $this->createMock(ArchiverInterface::class);
        $archiver->expects(self::once())->method('revive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->revive($this->createMock(PostRepositoryInterface::class));
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
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(ThreadRepositoryInterface::class)
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

        $this->component->pin($this->createMock(PostRepositoryInterface::class));
    }

    public function testUnpinShouldRunPinnersUnpin(): void
    {
        $pinner = $this->createMock(PinnerInterface::class);
        $pinner->expects(self::once())->method('unpin')->willReturn(PodiumResponse::success());
        $this->component->pinnerConfig = $pinner;

        $this->component->unpin($this->createMock(PostRepositoryInterface::class));
    }

    public function testGetLikerShouldThrowExceptionWhenLikerIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->likerConfig = '';

        $this->component->getLiker();
    }

    public function testThumbUpShouldRunLikersThumbUp(): void
    {
        $liker = $this->createMock(LikerInterface::class);
        $liker->expects(self::once())->method('thumbUp')->willReturn(PodiumResponse::success());
        $this->component->likerConfig = $liker;
        $this->component->thumbRepositoryConfig = $this->createMock(ThumbRepositoryInterface::class);

        $this->component->thumbUp(
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testThumbDownShouldRunLikersThumbDown(): void
    {
        $liker = $this->createMock(LikerInterface::class);
        $liker->expects(self::once())->method('thumbDown')->willReturn(PodiumResponse::success());
        $this->component->likerConfig = $liker;
        $this->component->thumbRepositoryConfig = $this->createMock(ThumbRepositoryInterface::class);

        $this->component->thumbDown(
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testThumbResetShouldRunLikersThumbReset(): void
    {
        $liker = $this->createMock(LikerInterface::class);
        $liker->expects(self::once())->method('thumbReset')->willReturn(PodiumResponse::success());
        $this->component->likerConfig = $liker;
        $this->component->thumbRepositoryConfig = $this->createMock(ThumbRepositoryInterface::class);

        $this->component->thumbReset(
            $this->createMock(PostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testGetPollBuilderShouldThrowExceptionWhenPollBuilderIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->pollBuilderConfig = '';

        $this->component->getPollBuilder();
    }

    public function testAddPollShouldRunPollBuilderCreate(): void
    {
        $builder = $this->createMock(PollBuilderInterface::class);
        $builder->expects(self::once())->method('create')->willReturn(PodiumResponse::success());
        $this->component->pollBuilderConfig = $builder;

        $this->component->addPoll($this->createMock(PollPostRepositoryInterface::class), []);
    }

    public function testEditPollShouldRunPollBuilderCreate(): void
    {
        $builder = $this->createMock(PollBuilderInterface::class);
        $builder->expects(self::once())->method('edit')->willReturn(PodiumResponse::success());
        $this->component->pollBuilderConfig = $builder;

        $this->component->editPoll($this->createMock(PollPostRepositoryInterface::class));
    }

    public function testGetPollRemoverShouldThrowExceptionWhenPollRemoverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->pollRemoverConfig = '';

        $this->component->getPollRemover();
    }

    public function testRemovePollShouldRunPollRemoverRemove(): void
    {
        $remover = $this->createMock(RemoverInterface::class);
        $remover->expects(self::once())->method('remove')->willReturn(PodiumResponse::success());
        $this->component->pollRemoverConfig = $remover;

        $this->component->removePoll($this->createMock(PollPostRepositoryInterface::class));
    }

    public function testGetPollVoterShouldThrowExceptionWhenPollVoterIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->pollVoterConfig = '';

        $this->component->getPollVoter();
    }

    public function testVotePollShouldRunPollVoterVote(): void
    {
        $voter = $this->createMock(VoterInterface::class);
        $voter->expects(self::once())->method('vote')->willReturn(PodiumResponse::success());
        $this->component->pollVoterConfig = $voter;

        $this->component->votePoll(
            $this->createMock(PollPostRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class),
            []
        );
    }
}
