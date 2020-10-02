[<<< Index](../README.md)

# Message

This component provides methods to manage the Podium messages.

## Usage

```
\Yii::$app->podium->message->...
```

## Configuration

#### archiverConfig

Archiver service. Expects an instance of [MessageArchiverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MessageArchiverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Message\MessageArchiver`.

#### messengerConfig

Messenger service. Expects an instance of [MessengerInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MessengerInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Message\MessageMessenger`.

#### removerConfig

Remover service. Expects an instance of [MessageRemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MessageRemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Message\MessageRemover`.

#### repositoryConfig

Message repository. Expects an instance of [MessageRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MessageRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [archive](#archive)
- [getArchiver](#getArchiver)
- [getMessenger](#getMessenger)
- [getRemover](#getRemover)
- [getRepository](#getRepository)
- [remove](#remove)
- [revive](#revive)
- [send](#send)

### archive <span id="archive"></span>

```
archive(
    Podium\Api\Interfaces\MessageRepositoryInterface $message,
    Podium\Api\Interfaces\MemberRepositoryInterface $participant
): Podium\Api\PodiumResponse
```

Archives the member's side of the message. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L130)

#### Events

- `Podium\Api\Services\Message\MessageArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Message\MessageArchiver::EVENT_AFTER_ARCHIVING`

---

### getArchiver <span id="getArchiver"></span>

```
getArchiver(): Podium\Api\Interfaces\MessageArchiverInterface
```

Returns the archiver service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L116)

---

### getMessenger <span id="getMessenger"></span>

```
getMessenger(): Podium\Api\Interfaces\MessengerInterface
```

Returns the messenger service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L64)

---

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\MessageRemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L92)

---

### getRepository <span id="getRepository"></span>

```
getRepository(): Podium\Api\Interfaces\MessageRepositoryInterface
```

Returns the message repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L48)

---

### remove <span id="remove"></span>

```
remove(
    Podium\Api\Interfaces\MessageRepositoryInterface $message,
    Podium\Api\Interfaces\MemberRepositoryInterface $participant
): Podium\Api\PodiumResponse
```

Removes the member's side of the message. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L106)

#### Events

- `Podium\Api\Services\Message\MessageRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Message\MessageRemover::EVENT_AFTER_REMOVING`

---

### revive <span id="revive"></span>

```
revive(
    Podium\Api\Interfaces\MessageRepositoryInterface $message,
    Podium\Api\Interfaces\MemberRepositoryInterface $participant
): Podium\Api\PodiumResponse
```

Revives the member's side of the message. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L138)

#### Events

- `Podium\Api\Services\Message\MessageArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Message\MessageArchiver::EVENT_AFTER_REVIVING`

---

### send <span id="send"></span>

```
send(
    Podium\Api\Interfaces\MemberRepositoryInterface $sender,
    Podium\Api\Interfaces\MemberRepositoryInterface $receiver,
    Podium\Api\Interfaces\MessageRepositoryInterface $replyTo = null,
    array $data = []
): Podium\Api\PodiumResponse
```

Send a new message (or a reply to the message) from the sender to the receiver. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Message.php#L78)

#### Events

- `Podium\Api\Services\Message\MessageMessenger::EVENT_BEFORE_SENDING`
- `Podium\Api\Services\Message\MessageMessenger::EVENT_AFTER_SENDING`

---

[Next >>> Post](post.md)
