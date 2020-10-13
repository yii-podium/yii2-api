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
- [getArchiver](#getarchiver)
- [getMessenger](#getmessenger)
- [getRemover](#getremover)
- [getRepository](#getrepository)
- [remove](#remove)
- [revive](#revive)
- [send](#send)

### archive

```
archive(
    Podium\Api\Interfaces\MessageRepositoryInterface $message,
    Podium\Api\Interfaces\MemberRepositoryInterface $participant
): Podium\Api\PodiumResponse
```

Archives the member's side of the message. See also [revive](#revive).

#### Events

- `Podium\Api\Services\Message\MessageArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Message\MessageArchiver::EVENT_AFTER_ARCHIVING`

---

### getArchiver

```
getArchiver(): Podium\Api\Interfaces\MessageArchiverInterface
```

Returns the archiver service which handles [archiving](#archive) and [reviving](#revive).

---

### getMessenger

```
getMessenger(): Podium\Api\Interfaces\MessengerInterface
```

Returns the messenger service which handles [sending](#send).

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\MessageRemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getRepository

```
getRepository(): Podium\Api\Interfaces\MessageRepositoryInterface
```

Returns the message repository.

---

### remove

```
remove(
    Podium\Api\Interfaces\MessageRepositoryInterface $message,
    Podium\Api\Interfaces\MemberRepositoryInterface $participant
): Podium\Api\PodiumResponse
```

Removes the member's side of the message. Only archived side of the message can be removed.

#### Events

- `Podium\Api\Services\Message\MessageRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Message\MessageRemover::EVENT_AFTER_REMOVING`

---

### revive

```
revive(
    Podium\Api\Interfaces\MessageRepositoryInterface $message,
    Podium\Api\Interfaces\MemberRepositoryInterface $participant
): Podium\Api\PodiumResponse
```

Revives the member's side of the message. See also [archive](#archive).

#### Events

- `Podium\Api\Services\Message\MessageArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Message\MessageArchiver::EVENT_AFTER_REVIVING`

---

### send

```
send(
    Podium\Api\Interfaces\MemberRepositoryInterface $sender,
    Podium\Api\Interfaces\MemberRepositoryInterface $receiver,
    Podium\Api\Interfaces\MessageRepositoryInterface $replyTo = null,
    array $data = []
): Podium\Api\PodiumResponse
```

Sends a new message (or a reply to the message) from the sender to the receiver if the receiver is not ignoring the 
sender. A message is saved in two copies for both sides so each side can be handled individually.

#### Events

- `Podium\Api\Services\Message\MessageMessenger::EVENT_BEFORE_SENDING`
- `Podium\Api\Services\Message\MessageMessenger::EVENT_AFTER_SENDING`

---

[<<< Index](../README.md) | [Next >>> Post](post.md)
