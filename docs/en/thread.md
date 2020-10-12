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
- [getArchiver](#getArchiver)
- [getBookmarkRepository](#getBookmarkRepository)
- [getBuilder](#getBuilder)
- [getHider](#getHider)
- [getLocker](#getLocker)
- [getMover](#getMover)
- [getPinner](#getPinner)
- [getRemover](#getRemover)
- [getSubscriptionRepository](#getSubscriptionRepository)
- [getThreadRepository](#getThreadRepository)
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

### archive <span id="archive"></span>

```
archive(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Archives the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L274)

#### Events

- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_AFTER_ARCHIVING`

---

### create <span id="create"></span>

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    Podium\Api\Interfaces\ForumRepositoryInterface $forum,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a thread as the author under the forum. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L127)

#### Events

- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_AFTER_CREATING`

---

### edit <span id="edit"></span>

```
edit(Podium\Api\Interfaces\ThreadRepositoryInterface $thread, array $data = []): Podium\Api\PodiumResponse
```

Edits the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L138)

#### Events

- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Thread\ThreadBuilder::EVENT_AFTER_EDITING`

---

### getArchiver <span id="getArchiver"></span>

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L260)

---

### getBookmarkRepository <span id="getBookmarkRepository"></span>

```
getBookmarkRepository(): Podium\Api\Interfaces\BookmarkRepositoryInterface
```

Returns the bookmark repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L356)

---

### getBuilder <span id="getBuilder"></span>

```
getBuilder(): Podium\Api\Interfaces\CategorisedBuilderInterface
```

Returns the builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L113)

---

### getHider <span id="getHider"></span>

```
getHider(): Podium\Api\Interfaces\Hiderface
```

Returns the hider service.

---

### getLocker <span id="getLocker"></span>

```
getLocker(): Podium\Api\Interfaces\LockerInterface
```

Returns the locker service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L228)

---

### getMover <span id="getMover"></span>

```
getMover(): Podium\Api\Interfaces\MoverInterface
```

Returns the mover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L172)

---

### getPinner <span id="getPinner"></span>

```
getPinner(): Podium\Api\Interfaces\PinnerInterface
```

Returns the pinner service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L196)

---

### getPostRepository <span id="getPostRepository"></span>

```
getPostRepository(): Podium\Api\Interfaces\PostRepositoryInterface
```

Returns the thread repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L98)

---

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L148)

---

### getSubscriptionRepository <span id="getSubscriptionRepository"></span>

```
getSubscriptionRepository(): Podium\Api\Interfaces\SubscriptionRepositoryInterface
```

Returns the subscription repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L308)

---

### getThreadRepository <span id="getThreadRepository"></span>

```
getThreadRepository(): Podium\Api\Interfaces\ThreadRepositoryInterface
```

Returns the thread repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L97)

---

### hide <span id="hide"></span>

```
hide(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Hides the thread.

#### Events

- `Podium\Api\Services\Thread\ThreadHider::EVENT_BEFORE_HIDING`
- `Podium\Api\Services\Thread\ThreadHider::EVENT_AFTER_HIDING`

---

### lock <span id="lock"></span>

```
lock(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Locks the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L242)

#### Events

- `Podium\Api\Services\Thread\ThreadLocker::EVENT_BEFORE_LOCKING`
- `Podium\Api\Services\Thread\ThreadLocker::EVENT_AFTER_LOCKING`

---

### mark <span id="mark"></span>

```
mark(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Marks the thread at the post's timestamp for the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L370)

#### Events

- `Podium\Api\Services\Thread\ThreadBookmarker::EVENT_BEFORE_MARKING`
- `Podium\Api\Services\Thread\ThreadBookmarker::EVENT_AFTER_MARKING`

---

### move <span id="move"></span>

```
move(
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    Podium\Api\Interfaces\ForumRepositoryInterface $forum
): Podium\Api\PodiumResponse
```

Moves the thread to the forum. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L186)

#### Events

- `Podium\Api\Services\Thread\ThreadMover::EVENT_BEFORE_MOVING`
- `Podium\Api\Services\Thread\ThreadMover::EVENT_AFTER_MOVING`

---

### pin <span id="pin"></span>

```
pin(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Pins the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L210)

#### Events

- `Podium\Api\Services\Thread\ThreadPinner::EVENT_BEFORE_PINNING`
- `Podium\Api\Services\Thread\ThreadPinner::EVENT_AFTER_PINNING`

---

### remove <span id="remove"></span>

```
remove(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Removes the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L162)

#### Events

- `Podium\Api\Services\Thread\ThreadRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Thread\ThreadRemover::EVENT_AFTER_REMOVING`

---

### reveal <span id="reveal"></span>

```
reveal(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Reveals the thread.

#### Events

- `Podium\Api\Services\Thread\ThreadHider::EVENT_BEFORE_REVEALING`
- `Podium\Api\Services\Thread\ThreadHider::EVENT_AFTER_REVEALING`

---

### revive <span id="revive"></span>

```
revive(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Revives the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L282)

#### Events

- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Thread\ThreadArchiver::EVENT_AFTER_REVIVING`

---

### subscribe <span id="subscribe"></span>

```
subscribe(
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Subscribes to the thread as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L322)

#### Events

- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_BEFORE_SUBSCRIBING`
- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_AFTER_SUBSCRIBING`

---

### unlock <span id="unlock"></span>

```
unlock(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Unlocks the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L250)

#### Events

- `Podium\Api\Services\Thread\ThreadLocker::EVENT_BEFORE_UNLOCKING`
- `Podium\Api\Services\Thread\ThreadLocker::EVENT_AFTER_UNLOCKING`

---

### unpin <span id="unpin"></span>

```
unpin(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Unpins the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L218)

#### Events

- `Podium\Api\Services\Thread\ThreadLiker::EVENT_BEFORE_THUMB_UP`
- `Podium\Api\Services\Thread\ThreadLiker::EVENT_AFTER_THUMB_UP`

---

### unsubscribe <span id="unsubscribe"></span>

```
unsubscribe(
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Unsubscribes from the thread as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Thread.php#L330)

#### Events

- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_BEFORE_UNSUBSCRIBING`
- `Podium\Api\Services\Thread\ThreadSubscriber::EVENT_AFTER_UNSUBSCRIBING`

---

[<<< Index](../README.md)
