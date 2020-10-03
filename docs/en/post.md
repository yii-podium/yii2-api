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

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Post\PostRemover`.

#### postRepositoryConfig

Post repository. Expects an instance of [PostRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/PostRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### thumbRepositoryConfig

Thumb repository. Expects an instance of [ThumbRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ThumbRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [addPoll](#addPoll)
- [archive](#archive)
- [create](#create)
- [edit](#edit)
- [editPoll](#editPoll)
- [getArchiver](#getArchiver)
- [getBuilder](#getBuilder)
- [getLiker](#getLiker)
- [getMover](#getMover)
- [getPinner](#getPinner)
- [getPollBuilder](#getPollBuilder)
- [getPollRemover](#getPollRemover)
- [getPollVoter](#getPollVoter)
- [getPostRepository](#getPostRepository)
- [getRemover](#getRemover)
- [getThumbRepository](#getThumbRepository)
- [move](#move)
- [pin](#pin)
- [remove](#remove)
- [removePoll](#removePoll)
- [revive](#revive)
- [thumbDown](#thumbDown)
- [thumbReset](#thumbReset)
- [thumbUp](#thumbUp)
- [unpin](#unpin)
- [votePoll](#votePoll)

### addPoll <span id="addPoll"></span>

```
addPoll(
    Podium\Api\Interfaces\PollPostRepositoryInterface $post,
    array $answers,
    array $data = []
): Podium\Api\PodiumResponse
```

Adds a poll with the poll answers to the post. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L331)

#### Events

- `Podium\Api\Services\Poll\PollBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Poll\PollBuilder::EVENT_AFTER_CREATING`

---

### archive <span id="archive"></span>

```
archive(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Archives the post. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L224)

#### Events

- `Podium\Api\Services\Post\PostArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Post\PostArchiver::EVENT_AFTER_ARCHIVING`

---

### create <span id="create"></span>

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a post as the author under the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L128)

#### Events

- `Podium\Api\Services\Post\PostBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Post\PostBuilder::EVENT_AFTER_CREATING`

---

### edit <span id="edit"></span>

```
edit(Podium\Api\Interfaces\PostRepositoryInterface $post, array $data = []): Podium\Api\PodiumResponse
```

Edits the post. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L139)

#### Events

- `Podium\Api\Services\Post\PostBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Post\PostBuilder::EVENT_AFTER_EDITING`

---

### editPoll <span id="editPoll"></span>

```
editPoll(Podium\Api\Interfaces\PollPostRepositoryInterface $post, array $answers = [], array $data = []): Podium\Api\PodiumResponse
```

Edits the post's poll with answers. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L339)

#### Events

- `Podium\Api\Services\Post\PostBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Post\PostBuilder::EVENT_AFTER_EDITING`

---

### getArchiver <span id="getArchiver"></span>

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L197)

---

### getBuilder <span id="getBuilder"></span>

```
getBuilder(): Podium\Api\Interfaces\CategorisedBuilderInterface
```

Returns the builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L114)

---

### getLiker <span id="getLiker"></span>

```
getLiker(): Podium\Api\Interfaces\LikerInterface
```

Returns the liker service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L229)

---

### getMover <span id="getMover"></span>

```
getMover(): Podium\Api\Interfaces\MoverInterface
```

Returns the mover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L173)

---

### getPinner <span id="getPinner"></span>

```
getPinner(): Podium\Api\Interfaces\PinnerInterface
```

Returns the pinner service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L285)

---

### getPollBuilder <span id="getPollBuilder"></span>

```
getPollBuilder(): Podium\Api\Interfaces\PollBuilderInterface
```

Returns the poll builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L317)

---

### getPollRemover <span id="getPollRemover"></span>

```
getPollRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the poll remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L349)

---

### getPollVoter <span id="getPollVoter"></span>

```
getPollVoter(): Podium\Api\Interfaces\VoterInterface
```

Returns the poll voter service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L373)

---

### getPostRepository <span id="getPostRepository"></span>

```
getPostRepository(): Podium\Api\Interfaces\PostRepositoryInterface
```

Returns the post repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L98)

---

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L149)

---

### getThumbRepository <span id="getThumbRepository"></span>

```
getThumbRepository(): Podium\Api\Interfaces\ThumbRepositoryInterface
```

Returns the thumb repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L245)

---

### move <span id="move"></span>

```
move(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\ThreadRepositoryInterface $thread
): Podium\Api\PodiumResponse
```

Moves the post to the thread. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L187)

#### Events

- `Podium\Api\Services\Post\PostMover::EVENT_BEFORE_MOVING`
- `Podium\Api\Services\Post\PostMover::EVENT_AFTER_MOVING`

---

### pin <span id="pin"></span>

```
pin(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Pins the post. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L299)

#### Events

- `Podium\Api\Services\Post\PostPinner::EVENT_BEFORE_PINNING`
- `Podium\Api\Services\Post\PostPinner::EVENT_AFTER_PINNING`

---

### remove <span id="remove"></span>

```
remove(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Removes the post. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L134)

#### Events

- `Podium\Api\Services\Post\PostRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Post\PostRemover::EVENT_AFTER_REMOVING`

---

### removePoll <span id="removePoll"></span>

```
removePoll(Podium\Api\Interfaces\PollPostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Removes the post's poll. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L363)

#### Events

- `Podium\Api\Services\Poll\PollRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Poll\PollRemover::EVENT_AFTER_REMOVING`

---

### revive <span id="revive"></span>

```
revive(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Revives the post. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L219)

#### Events

- `Podium\Api\Services\Post\PostArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Post\PostArchiver::EVENT_AFTER_REVIVING`

---

### thumbDown <span id="thumbDown"></span>

```
thumbDown(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Gives a thumb down to the post as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L267)

#### Events

- `Podium\Api\Services\Post\PostLiker::EVENT_BEFORE_THUMB_DOWN`
- `Podium\Api\Services\Post\PostLiker::EVENT_AFTER_THUMB_DOWN`

---

### thumbReset <span id="thumbReset"></span>

```
thumbReset(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Resets the thumb for the post as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L275)

#### Events

- `Podium\Api\Services\Post\PostLiker::EVENT_BEFORE_THUMB_RESET`
- `Podium\Api\Services\Post\PostLiker::EVENT_AFTER_THUMB_RESET`

---

### thumbUp <span id="thumbUp"></span>

```
thumbUp(
    Podium\Api\Interfaces\PostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Gives a thumb up to the post as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L259)

#### Events

- `Podium\Api\Services\Post\PostLiker::EVENT_BEFORE_THUMB_UP`
- `Podium\Api\Services\Post\PostLiker::EVENT_AFTER_THUMB_UP`

---

### unpin <span id="unpin"></span>

```
unpin(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Unpins the post. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L307)

#### Events

- `Podium\Api\Services\Post\PostPinner::EVENT_BEFORE_UNPINNING`
- `Podium\Api\Services\Post\PostPinner::EVENT_AFTER_UNPINNING`

---

### votePoll <span id="votePoll"></span>

```
votePoll(
    Podium\Api\Interfaces\PollPostRepositoryInterface $post,
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    array $answers
): Podium\Api\PodiumResponse
```

Votes in the post's poll with answers as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Post.php#L387)

#### Events

- `Podium\Api\Services\Poll\PollVoter::EVENT_BEFORE_VOTING`
- `Podium\Api\Services\Poll\PollVoter::EVENT_AFTER_VOTING`

---

[<<< Index](../README.md) | [Next >>> Rank](rank.md)
