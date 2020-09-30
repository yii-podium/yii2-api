<?php

declare(strict_types=1);

namespace Podium\Tests;

use PHPUnit\Framework\TestCase;
use Yii;
use yii\console\Application;
use yii\db\Connection;
use yii\db\Transaction;
use yii\i18n\PhpMessageSource;
use yii\log\Logger;

class AppTestCase extends TestCase
{
    public Transaction $transaction;

    public Logger $logger;

    public static function setUpBeforeClass(): void
    {
        new Application(
            [
                'id' => 'PodiumAPITest',
                'basePath' => __DIR__,
                'vendorPath' => __DIR__ . '/../vendor/',
                'components' => [
                    'i18n' => [
                        'translations' => [
                            'podium.*' => [
                                'class' => PhpMessageSource::class,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public static function tearDownAfterClass(): void
    {
        Yii::$app = null;
    }

    protected function setUp(): void
    {
        $this->transaction = $this->createMock(Transaction::class);
        $connection = $this->createMock(Connection::class);
        $connection->method('beginTransaction')->willReturn($this->transaction);
        Yii::$app->set('db', $connection);

        $this->logger = $this->createMock(Logger::class);
        Yii::setLogger($this->logger);
    }
}
