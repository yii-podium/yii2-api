<?php

declare(strict_types=1);

namespace Podium\Tests\Unit\Message;

use PHPUnit\Framework\TestCase;
use Podium\Api\Components\Message;
use Podium\Api\Components\PodiumResponse;
use Podium\Api\Interfaces\MemberRepositoryInterface;
use Podium\Api\Interfaces\MessageArchiverInterface;
use Podium\Api\Interfaces\MessageRemoverInterface;
use Podium\Api\Interfaces\MessageRepositoryInterface;
use Podium\Api\Interfaces\MessengerInterface;
use yii\base\InvalidConfigException;

class MessageComponentTest extends TestCase
{
    private Message $component;

    protected function setUp(): void
    {
        $this->component = new Message();
    }

    public function testGetMessengerShouldThrowExceptionWhenMessengerIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->messengerConfig = '';

        $this->component->getMessenger();
    }

    public function testSendShouldRunMessengersSend(): void
    {
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger->expects(self::once())->method('send')->willReturn(PodiumResponse::success());
        $this->component->messengerConfig = $messenger;
        $this->component->repositoryConfig = $this->createMock(MessageRepositoryInterface::class);

        $member = $this->createMock(MemberRepositoryInterface::class);
        $this->component->send($member, $member);
    }

    public function testGetRemoverShouldThrowExceptionWhenRemoverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->removerConfig = '';

        $this->component->getRemover();
    }

    public function testRemoveShouldRunRemoversRemove(): void
    {
        $remover = $this->createMock(MessageRemoverInterface::class);
        $remover->expects(self::once())->method('remove')->willReturn(PodiumResponse::success());
        $this->component->removerConfig = $remover;

        $this->component->remove(
            $this->createMock(MessageRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testGetArchiverShouldThrowExceptionWhenArchiverIsMisconfigured(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->component->archiverConfig = '';

        $this->component->getArchiver();
    }

    public function testArchiveShouldRunArchiversArchive(): void
    {
        $archiver = $this->createMock(MessageArchiverInterface::class);
        $archiver->expects(self::once())->method('archive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->archive(
            $this->createMock(MessageRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }

    public function testReviveShouldRunArchiversRevive(): void
    {
        $archiver = $this->createMock(MessageArchiverInterface::class);
        $archiver->expects(self::once())->method('revive')->willReturn(PodiumResponse::success());
        $this->component->archiverConfig = $archiver;

        $this->component->revive(
            $this->createMock(MessageRepositoryInterface::class),
            $this->createMock(MemberRepositoryInterface::class)
        );
    }
}
