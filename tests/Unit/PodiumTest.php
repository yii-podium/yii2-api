<?php

declare(strict_types=1);

namespace Podium\Tests\Unit;

use Podium\Api\Components\Account;
use Podium\Api\Components\Category;
use Podium\Api\Components\Forum;
use Podium\Api\Components\Group;
use Podium\Api\Components\Member;
use Podium\Api\Components\Message;
use Podium\Api\Components\Post;
use Podium\Api\Components\Rank;
use Podium\Api\Components\Thread;
use Podium\Api\Module as Podium;
use Podium\Tests\AppTestCase;
use Yii;

class PodiumTest extends AppTestCase
{
    private Podium $podium;

    protected function setUp(): void
    {
        $this->podium = new Podium();
    }

    public function testCoreComponents(): void
    {
        self::assertSame(
            [
                'account' => [
                    'class' => Account::class,
                    'podiumBridge' => true,
                ],
                'category' => ['class' => Category::class],
                'forum' => ['class' => Forum::class],
                'group' => ['class' => Group::class],
                'member' => ['class' => Member::class],
                'message' => ['class' => Message::class],
                'post' => ['class' => Post::class],
                'rank' => ['class' => Rank::class],
                'thread' => ['class' => Thread::class],
            ],
            $this->podium->coreComponents()
        );
    }

    public function testI18nInit(): void
    {
        $translations = Yii::$app->getI18n()->translations['podium.*'];

        self::assertSame('yii\i18n\PhpMessageSource', $translations['class']);
        self::assertSame('en', $translations['sourceLanguage']);
        self::assertTrue($translations['forceTranslation']);
        self::assertStringEndsWith('/src/messages', $translations['basePath']);
    }

    public function testCompleteComponent(): void
    {
        self::assertInstanceOf(Podium::class, $this->podium->getAccount()->getPodium());
    }

    public function testSettingCore(): void
    {
        $components = $this->podium->components;

        self::assertArrayHasKey('account', $components);
        self::assertArrayHasKey('category', $components);
        self::assertArrayHasKey('forum', $components);
        self::assertArrayHasKey('group', $components);
        self::assertArrayHasKey('member', $components);
        self::assertArrayHasKey('message', $components);
        self::assertArrayHasKey('post', $components);
        self::assertArrayHasKey('rank', $components);
        self::assertArrayHasKey('thread', $components);
    }

    public function testGetAccount(): void
    {
        self::assertInstanceOf(Account::class, $this->podium->getAccount());
    }

    public function testGetCategory(): void
    {
        self::assertInstanceOf(Category::class, $this->podium->getCategory());
    }

    public function testGetForum(): void
    {
        self::assertInstanceOf(Forum::class, $this->podium->getForum());
    }

    public function testGetGroup(): void
    {
        self::assertInstanceOf(Group::class, $this->podium->getGroup());
    }

    public function testGetMember(): void
    {
        self::assertInstanceOf(Member::class, $this->podium->getMember());
    }

    public function testGetMessage(): void
    {
        self::assertInstanceOf(Message::class, $this->podium->getMessage());
    }

    public function testGetPost(): void
    {
        self::assertInstanceOf(Post::class, $this->podium->getPost());
    }

    public function testGetRank(): void
    {
        self::assertInstanceOf(Rank::class, $this->podium->getRank());
    }

    public function testGetThread(): void
    {
        self::assertInstanceOf(Thread::class, $this->podium->getThread());
    }
}
