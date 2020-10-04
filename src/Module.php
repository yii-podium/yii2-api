<?php

declare(strict_types=1);

namespace Podium\Api;

use Podium\Api\Components\Account;
use Podium\Api\Components\Category;
use Podium\Api\Components\Forum;
use Podium\Api\Components\Group;
use Podium\Api\Components\Logger;
use Podium\Api\Components\Member;
use Podium\Api\Components\Message;
use Podium\Api\Components\Post;
use Podium\Api\Components\Rank;
use Podium\Api\Components\Thread;
use Podium\Api\Interfaces\AccountInterface;
use Podium\Api\Interfaces\CategoryInterface;
use Podium\Api\Interfaces\ForumInterface;
use Podium\Api\Interfaces\GroupInterface;
use Podium\Api\Interfaces\LoggerInterface;
use Podium\Api\Interfaces\MemberInterface;
use Podium\Api\Interfaces\MessageInterface;
use Podium\Api\Interfaces\PostInterface;
use Podium\Api\Interfaces\RankInterface;
use Podium\Api\Interfaces\ThreadInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\ServiceLocator;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;

use function is_array;

/**
 * Podium API
 * Yii 2 Forum Engine.
 *
 * @author Paweł Bizley Brzozowski <pawel@positive.codes>
 *
 * @version 1.0.0
 *
 * @license Apache License 2.0
 *
 * https://github.com/yii-podium/yii2-api
 * Please report all issues at GitHub
 * https://github.com/yii-podium/yii2-api/issues
 *
 * Podium requires Yii 2
 * http://www.yiiframework.com
 * https://github.com/yiisoft/yii2
 *
 * @property AccountInterface  $account
 * @property CategoryInterface $category
 * @property ForumInterface    $forum
 * @property GroupInterface    $group
 * @property LoggerInterface   $logger
 * @property MemberInterface   $member
 * @property MessageInterface  $message
 * @property PostInterface     $post
 * @property RankInterface     $rank
 * @property ThreadInterface   $thread
 */
class Module extends ServiceLocator
{
    private string $version = '0.1.0';

    public function getVersion(): string
    {
        return $this->version;
    }

    public function __construct(array $config = [])
    {
        foreach ($this->coreComponents() as $id => $component) {
            if (!isset($config['components'][$id])) {
                $config['components'][$id] = $component;
            } elseif (is_array($config['components'][$id]) && !isset($config['components'][$id]['class'])) {
                $config['components'][$id]['class'] = $component['class'];
            }
        }

        parent::__construct($config);
    }

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        $this->prepareTranslations();
        $this->completeComponents();
    }

    /**
     * Returns the configuration of core Podium components.
     */
    public function coreComponents(): array
    {
        return [
            'account' => [
                'class' => Account::class,
                'podiumBridge' => true,
            ],
            'category' => ['class' => Category::class],
            'forum' => ['class' => Forum::class],
            'group' => ['class' => Group::class],
            'logger' => ['class' => Logger::class],
            'member' => ['class' => Member::class],
            'message' => ['class' => Message::class],
            'post' => ['class' => Post::class],
            'rank' => ['class' => Rank::class],
            'thread' => ['class' => Thread::class],
        ];
    }

    /**
     * Returns account component.
     *
     * @return AccountInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getAccount()
    {
        return $this->get('account');
    }

    /**
     * Returns category component.
     *
     * @return CategoryInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * Returns forum component.
     *
     * @return ForumInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getForum()
    {
        return $this->get('forum');
    }

    /**
     * Returns group component.
     *
     * @return GroupInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getGroup()
    {
        return $this->get('group');
    }

    /**
     * Returns logger component.
     *
     * @return LoggerInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * Returns member component.
     *
     * @return MemberInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getMember()
    {
        return $this->get('member');
    }

    /**
     * Returns message component.
     *
     * @return MessageInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getMessage()
    {
        return $this->get('message');
    }

    /**
     * Returns post component.
     *
     * @return PostInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getPost()
    {
        return $this->get('post');
    }

    /**
     * Returns rank component.
     *
     * @return RankInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getRank()
    {
        return $this->get('rank');
    }

    /**
     * Returns thread component.
     *
     * @return ThreadInterface|object|null
     *
     * @throws InvalidConfigException
     */
    public function getThread()
    {
        return $this->get('thread');
    }

    protected function prepareTranslations(): void
    {
        Yii::$app->getI18n()->translations['podium.error'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'forceTranslation' => true,
            'basePath' => __DIR__.'/Messages',
        ];
    }

    /**
     * Sets Podium reference for custom components.
     *
     * @throws InvalidConfigException
     */
    protected function completeComponents(): void
    {
        $components = $this->getComponents();

        foreach ($components as $id => $component) {
            if (ArrayHelper::remove($component, 'podiumBridge', false)) {
                $component['podium'] = $this;
                $this->set($id, $component);
            }
        }
    }
}
