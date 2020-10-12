[<<< Index](../README.md)

# Post

This component provides methods to manage the Podium posts.

## Usage

```
\Yii::$app->podium->post->...
```

## Configuration

#### archiverConfig

Archiver service. Expects an instance of [ArchiverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ArchiverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Post\PostArchiver`.

#### builderConfig

Builder service. Expects an instance of [CategorisedBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CategorisedBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Post\PostBuilder`.

#### likerConfig

Liker service. Expects an instance of [LikerInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/LikerInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Post\PostLiker`.

#### moverConfig

Mover service. Expects an instance of [MoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Post\PostMover`.

#### pinnerConfig

Pinner service. Expects an instance of [PinnerInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/PinnerInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Post\PostPinner`.

#### pollBuilderConfig

Poll Builder service. Expects an instance of [PollBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/PollBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Poll\PollBuilder`.

#### pollRemoverConfig

Poll Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Poll\PollRemover`.

#### pollVoterConfig

Poll Voter service. Expects an instance of [VoterInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/VoterInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Poll\PollVoter`.

#### postRepositoryConfig

Post repository. Expects an instance of [PostRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/PostRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Post\PostRemover`.

#### thumbRepositoryConfig

Thumb repository. Expects an instance of [ThumbRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ThumbRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [addPoll](#addpoll)
- [archive](#archive)
- [create](#create)
- [edit](#edit)
- [editPoll](#editpoll)
- [getArchiver](#getarchiver)
- [getBuilder](#getbuilder)
- [getLiker](#getliker)
- [getMover](#getmover)
- [getPinner](#getpinner)
- [getPollBuilder](#getpollbuilder)
- [getPollRemover](#getpollremover)
- [getPollVoter](#getpollvoter)
- [getPostRepository](#getpostrepository)
- [getRemover](#getremover)
- [getThumbRepository](#getthumbrepository)
- [move](#move)
- [pin](#pin)
- [remove](#remove)
- [removePoll](#removepoll)
- [revive](#revive)
- [thumbDown](#thumbdown)
- [thumbReset](#thumbreset)
- [thumbUp](#thumbup)
- [unpin](#unpin)
- [votePoll](#votepoll)

### addPoll

```
addPoll(
    Podium\Api\Interfaces\PollPostRepositoryInterface $post,
    array $answers,
    array $data = []
): Podium\Api\PodiumResponse
```

Adds a poll with the poll answers to the post. See also [editPoll](#editpoll).

Required data:
- `question` - the text that will be displayed above the answers
- `expires_at` - the timestamp for voting end time

Optional data (with defaults):
- `revealed` (`true`) - whether the poll results should be revealed to the member after voting or kept secret until expiration date
- `choice_id` (`single`) - whether the poll is single or multiple choice

#### Events

- `Podium\Api\Services\Poll\PollBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Poll\PollBuilder::EVENT_AFTER_CREATING`

---

### archive

```
archive(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Archives the post. See also [revive](#revive).

#### Events

- `Podium\Api\Services\Post\PostArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Post\PostArchiver::EVENT_AFTER_ARCHIVING`

---

### create

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a post as the author under the thread. See also [edit](#edit).
                                               
Required data:
- `content`

#### Events

- `Podium\Api\Services\Post\PostBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Post\PostBuilder::EVENT_AFTER_CREATING`

---

### edit

```
edit(Podium\Api\Interfaces\PostRepositoryInterface $post, array $data = []): Podium\Api\PodiumResponse
```

Edits the post. See also [create](#create).

Optional data:
- `content`

#### Events

- `Podium\Api\Services\Post\PostBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Post\PostBuilder::EVENT_AFTER_EDITING`

---

### editPoll

```
editPoll(
    Podium\Api\Interfaces\PollPostRepositoryInterface $post,
    array $answers = [],
    array $data = []
): Podium\Api\PodiumResponse
```

Edits the post's poll with answers. See also [addPoll](#addpoll).

Optional data:
- `question` - the text that will be displayed above the answers
- `expires_at` - the timestamp for voting end time
- `revealed` - whether the poll results should be revealed to the member after voting or kept secret until expiration date
- `choice_id` - whether the poll is single or multiple choice (changing not possible if members already voted)

#### Events

- `Podium\Api\Services\Post\PostBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Post\PostBuilder::EVENT_AFTER_EDITING`

---

### getArchiver

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service which handles [archiving](#archive) and [reviving](#revive).

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\CategorisedBuilderInterface
```

Returns the builder service which handles [creating](#create) and [editing](#edit).

---

### getLiker

```
getLiker(): Podium\Api\Interfaces\LikerInterface
```

Returns the liker service which handles giving [thumb up](#thumbup), [thumb down](#thumbdown), and [resetting thumb](#thumbreset).

---

### getMover

```
getMover(): Podium\Api\Interfaces\MoverInterface
```

Returns the mover service which handles [moving](#move) a post between threads.

---

### getPinner

```
getPinner(): Podium\Api\Interfaces\PinnerInterface
```

Returns the pinner service which handles [pinning](#pin) and [unpinning](#unpin).

---

### getPollBuilder

```
getPollBuilder(): Podium\Api\Interfaces\PollBuilderInterface
```

Returns the poll builder service which handles [adding](#addpoll) and [editing](#editpoll) the poll.

---

### getPollRemover

```
getPollRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the poll remover service which handles [removing](#removepoll) the poll.

---

### getPollVoter

```
getPollVoter(): Podium\Api\Interfaces\VoterInterface
```

Returns the poll voter service which handles [voting](#votepoll) in polls.

---

### getPostRepository

```
getPostRepository(): Podium\Api\Interfaces\PostRepositoryInterface
```

Returns the post repository.

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getThumbRepository

```
getThumbRepository(): Podium\Api\Interfaces\ThumbRepositoryInterface
```

Returns the thumb repository.

---

### move

```
move(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread
): Podium\Api\PodiumResponse
```

Moves the post to the thread.

#### Events

- `Podium\Api\Services\Post\PostMover::EVENT_BEFORE_MOVING`
- `Podium\Api\Services\Post\PostMover::EVENT_AFTER_MOVING`

---

### pin

```
pin(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Pins the post. See also [unpin](#unpin).

#### Events

- `Podium\Api\Services\Post\PostPinner::EVENT_BEFORE_PINNING`
- `Podium\Api\Services\Post\PostPinner::EVENT_AFTER_PINNING`

---

### remove

```
remove(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Removes the post.

#### Events

- `Podium\Api\Services\Post\PostRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Post\PostRemover::EVENT_AFTER_REMOVING`

---

### removePoll

```
removePoll(Podium\Api\Interfaces\PollPostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Removes the post's poll.

#### Events

- `Podium\Api\Services\Poll\PollRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Poll\PollRemover::EVENT_AFTER_REMOVING`

---

### revive

```
revive(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Revives the post. See also [archive](#archive).

#### Events

- `Podium\Api\Services\Post\PostArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Post\PostArchiver::EVENT_AFTER_REVIVING`

---

### thumbDown

```
thumbDown(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Gives a thumb down to the post as the member. See also [thumbUp](#thumbup) and [thumbReset](#thumbreset).

#### Events

- `Podium\Api\Services\Post\PostLiker::EVENT_BEFORE_THUMB_DOWN`
- `Podium\Api\Services\Post\PostLiker::EVENT_AFTER_THUMB_DOWN`

---

### thumbReset

```
thumbReset(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Resets the thumb for the post as the member. See also [thumbUp](#thumbup) and [thumbDown](#thumbdown).

#### Events

- `Podium\Api\Services\Post\PostLiker::EVENT_BEFORE_THUMB_RESET`
- `Podium\Api\Services\Post\PostLiker::EVENT_AFTER_THUMB_RESET`

---

### thumbUp

```
thumbUp(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Gives a thumb up to the post as the member. See also [thumbDown](#thumbdown) and [thumbReset](#thumbreset).

#### Events

- `Podium\Api\Services\Post\PostLiker::EVENT_BEFORE_THUMB_UP`
- `Podium\Api\Services\Post\PostLiker::EVENT_AFTER_THUMB_UP`

---

### unpin

```
unpin(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Unpins the post. See also [pin](#pin).

#### Events

- `Podium\Api\Services\Post\PostPinner::EVENT_BEFORE_UNPINNING`
- `Podium\Api\Services\Post\PostPinner::EVENT_AFTER_UNPINNING`

---

### votePoll

```
votePoll(
    Podium\Api\Interfaces\PollPostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    array $answers
): Podium\Api\PodiumResponse
```

Votes in the post's poll with answers as the member.

#### Events

- `Podium\Api\Services\Poll\PollVoter::EVENT_BEFORE_VOTING`
- `Podium\Api\Services\Poll\PollVoter::EVENT_AFTER_VOTING`

---

[<<< Index](../README.md) | [Next >>> Rank](rank.md)
