[<<< Index](../README.md)

# Thread

This component provides methods to manage the Podium threads.

## Usage

```
\Yii::$app->podium->thread->...
```

## Configuration

#### archiverConfig

Archiver service. Expects an instance of [ArchiverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ArchiverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadArchiver`.

#### bookmarkerConfig

Bookmarker service. Expects an instance of [BookmarkerInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/BookmarkerInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadBookmarker`.

#### bookmarkRepositoryConfig

Bookmark repository. Expects an instance of [BookmarkRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/BookmarkRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### builderConfig

Builder service. Expects an instance of [CategorisedBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CategorisedBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadBuilder`.

#### hiderConfig

Hider service. Expects an instance of [HiderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/HiderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadHider`.

#### lockerConfig

Locker service. Expects an instance of [LockerInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/LockerInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadLocker`.

#### moverConfig

Mover service. Expects an instance of [MoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadMover`.

#### pinnerConfig

Pinner service. Expects an instance of [PinnerInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/PinnerInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadPinner`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadRemover`.

#### subscriberConfig

Subscriber service. Expects an instance of [SubscriberInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/SubscriberInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Thread\ThreadSubscriber`.

#### subscriptionRepositoryConfig

Subscription repository. Expects an instance of 
[SubscriptionRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/SubscriptionRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### threadRepositoryConfig

Thread repository. Expects an instance of [ThreadRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ThreadRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [archive](#archive)
- [create](#create)
- [edit](#edit)
- [getArchiver](#getarchiver)
- [getBookmarkRepository](#getbookmarkrepository)
- [getBuilder](#getbuilder)
- [getHider](#gethider)
- [getLocker](#getlocker)
- [getMover](#getmover)
- [getPinner](#getpinner)
- [getRemover](#getremover)
- [getSubscriptionRepository](#getsubscriptionrepository)
- [getThreadRepository](#getthreadrepository)
- [hide](#hide)
- [lock](#lock)
- [mark](#mark)
- [move](#move)
- [pin](#pin)
- [remove](#remove)
- [reveal](#reveal)
- [revive](#revive)
- [subscribe](#subscribe)
- [unlock](#unlock)
- [unpin](#unpin)
- [unsubscribe](#unsubscribe)

### archive

```
archive(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Archives the thread. Only archived threads can be removed. Archiving a thread does not archive its child posts. See also 
[revive](#revive).

#### Events

- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_AFTER_ARCHIVING`

---

### create

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    Podium\Api\Interfaces\ForumRepositoryInterface $forum,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a thread as the author under the forum. See also [edit](#edit).

#### Events

- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_AFTER_CREATING`

---

### edit

```
edit(Podium\Api\Interfaces\ThreadRepositoryInterface $thread, array $data = []): Podium\Api\PodiumResponse
```

Edits the thread. See also [create](#create).

#### Events

- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_AFTER_EDITING`

---

### getArchiver

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service which handles [archiving](#archive) and [reviving](#revive).

---

### getBookmarkRepository

```
getBookmarkRepository(): Podium\Api\Interfaces\BookmarkRepositoryInterface
```

Returns the bookmark repository.

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\CategorisedBuilderInterface
```

Returns the builder service which handles [creating](#create) and [editing](#edit).

---

### getHider

```
getHider(): Podium\Api\Interfaces\Hiderface
```

Returns the hider service which handles [hiding](#hide) and [revealing](#reveal).

---

### getLocker

```
getLocker(): Podium\Api\Interfaces\LockerInterface
```

Returns the locker service which handles [locking](#lock) and [unlocking](#unlock).

---

### getMover

```
getMover(): Podium\Api\Interfaces\MoverInterface
```

Returns the mover service which handles [moving](#move) a thread between forums.

---

### getPinner

```
getPinner(): Podium\Api\Interfaces\PinnerInterface
```

Returns the pinner service which handles [pinning](#pin) and [unpinning](#unpin).

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getSubscriptionRepository

```
getSubscriptionRepository(): Podium\Api\Interfaces\SubscriptionRepositoryInterface
```

Returns the subscription repository.

---

### getThreadRepository

```
getThreadRepository(): Podium\Api\Interfaces\ThreadRepositoryInterface
```

Returns the thread repository.

---

### hide

```
hide(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Hides the thread. Thread can be hidden from certain groups of users. See also [reveal](#reveal).

#### Events

- `Podium\Api\Services\Thread\ThreadHider::EVENT_BEFORE_HIDING`
- `Podium\Api\Services\Thread\ThreadHider::EVENT_AFTER_HIDING`

---

### lock

```
lock(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Locks the thread. The locked thread does not accept new posts. See also [unlock](#unlock).

#### Events

- `Podium\Api\Services\Thread\ThreadLocker::EVENT_BEFORE_LOCKING`
- `Podium\Api\Services\Thread\ThreadLocker::EVENT_AFTER_LOCKING`

---

### mark

```
mark(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Marks the thread at the post's timestamp for the member.

#### Events

- `Podium\Api\Services\Thread\ThreadBookmarker::EVENT_BEFORE_MARKING`
- `Podium\Api\Services\Thread\ThreadBookmarker::EVENT_AFTER_MARKING`

---

### move

```
move(
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    Podium\Api\Interfaces\ForumRepositoryInterface $forum
): Podium\Api\PodiumResponse
```

Moves the thread to the forum.

#### Events

- `Podium\Api\Services\Thread\ThreadMover::EVENT_BEFORE_MOVING`
- `Podium\Api\Services\Thread\ThreadMover::EVENT_AFTER_MOVING`

---

### pin

```
pin(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Pins the thread. See also [unpin](#unpin).

#### Events

- `Podium\Api\Services\Thread\ThreadPinner::EVENT_BEFORE_PINNING`
- `Podium\Api\Services\Thread\ThreadPinner::EVENT_AFTER_PINNING`

---

### remove

```
remove(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Removes the thread. Only archived threads can be removed. Removing a thread removes all its child posts, regardless of 
their archived status.

#### Events

- `Podium\Api\Services\Thread\ThreadRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Thread\ThreadRemover::EVENT_AFTER_REMOVING`

---

### reveal

```
reveal(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Reveals the thread. Thread that is not hidden (default state) is available for all groups of users. See also [hide](#hide).

#### Events

- `Podium\Api\Services\Thread\ThreadHider::EVENT_BEFORE_REVEALING`
- `Podium\Api\Services\Thread\ThreadHider::EVENT_AFTER_REVEALING`

---

### revive

```
revive(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Revives the thread. The revived thread is no longer archived. It does not affect the archived status of its child posts. 
See also [archive](#archive).

#### Events

- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_AFTER_REVIVING`

---

### subscribe

```
subscribe(
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Subscribes to the thread as the member. See also [unsubscribe](#unsubscribe).

#### Events

- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING`
- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_AFTER_SUBSCRIBING`

---

### unlock

```
unlock(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Unlocks the thread. See also [lock](#lock).

#### Events

- `Podium\Api\Services\Thread\ThreadLocker::EVENT_BEFORE_UNLOCKING`
- `Podium\Api\Services\Thread\ThreadLocker::EVENT_AFTER_UNLOCKING`

---

### unpin

```
unpin(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Unpins the thread. See also [pin](#pin).

#### Events

- `Podium\Api\Services\Thread\ThreadLiker::EVENT_BEFORE_THUMB_UP`
- `Podium\Api\Services\Thread\ThreadLiker::EVENT_AFTER_THUMB_UP`

---

### unsubscribe

```
unsubscribe(
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Unsubscribes from the thread as the member. See also [subscribe](#subscribe).

#### Events

- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING`
- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING`

---

[<<< Index](../README.md)
