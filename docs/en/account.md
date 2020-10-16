[<<< Index](../README.md)

# Account

This component provides handy shortcut to other components that are using 
[MemberRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MemberRepositoryInterface.php) 
and allows to call their methods from the perspective of a logged-in user.

## Usage

```
\Yii::$app->podium->account->...
```

## Configuration

#### repositoryConfig

Member repository. Expects an instance of [MemberRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MemberRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### userConfig

User component. Expects an instance of [User](https://github.com/yiisoft/yii2/blob/master/framework/web/User.php) or 
component's ID or configuration array that can be resolved as the above. Default: `user`.

## Methods

- [archiveMessage](#archivemessage)
- [befriendMember](#befriendmember)
- [createCategory](#createcategory)
- [createForum](#createforum)
- [createPost](#createpost)
- [createThread](#createthread)
- [disconnectMember](#disconnectmember)
- [edit](#edit)
- [getMembership](#getmembership)
- [getPodium](#getpodium)
- [ignoreMember](#ignoremember)
- [joinGroup](#joingroup)
- [leaveGroup](#leavegroup)
- [log](#log)
- [markThread](#markthread)
- [removeMessage](#removemessage)
- [reviveMessage](#revivemessage)
- [sendMessage](#sendmessage)
- [setPodium](#setpodium)
- [subscribeThread](#subscribethread)
- [thumbDownPost](#thumbdownpost)
- [thumbResetPost](#thumbresetpost)
- [thumbUpPost](#thumbuppost)
- [unsubscribeThread](#unsubscribethread)
- [votePoll](#votepoll)

### archiveMessage

```
archiveMessage(Podium\Api\Interfaces\MessageRepositoryInterface $message): Podium\Api\PodiumResponse
```

Archives the current user's side of the message. See [archive](message.md#archive).

---

### befriendMember

```
befriendMember(Podium\Api\Interfaces\MemberRepositoryInterface $target): Podium\Api\PodiumResponse
```

Befriends the target member as the current user. See [befriend](member.md#befriend).

---

### createCategory

```
createCategory(array $data = []): Podium\Api\PodiumResponse
```

Creates a category as the current user. See [create](category.md#create).

---

### createForum

```
createForum(
    Podium\Api\Interfaces\CategoryRepositoryInterface $parentCategory,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a forum under the parent category as the current user. See [create](forum.md#create).

---

### createPost

```
createPost(Podium\Api\Interfaces\ThreadRepositoryInterface $parentThread, array $data = []): Podium\Api\PodiumResponse
```

Creates a post under the parent thread as the current user. See [create](post.md#create).

---

### createThread

```
createThread(Podium\Api\Interfaces\ForumRepositoryInterface $parentForum, array $data = []): Podium\Api\PodiumResponse
```

Creates a thread under the parent forum as the current user. See [create](thread.md#create).

---

### disconnectMember

```
disconnectMember(Podium\Api\Interfaces\MemberRepositoryInterface $target): Podium\Api\PodiumResponse
```

Disconnects the target member from the current user. See [disconnect](member.md#disconnect).

---

### edit

```
edit(array $data = []): Podium\Api\PodiumResponse
```

Edits the current user data. See [edit](member.md#edit).

---

### getMembership

```
getMembership(bool $renew = false): Podium\Api\Interfaces\MemberRepositoryInterface
```

Returns member's repository loaded with current user's data.

---

### getPodium

```
getPodium: Podium\Api\Podium
```

Returns Podium component.

---

### ignoreMember

```
ignoreMember(Podium\Api\Interfaces\MemberRepositoryInterface $target): Podium\Api\PodiumResponse
```

Ignores the target member as the current user. See [ignore](member.md#ignore).

---

### joinGroup

```
joinGroup(Podium\Api\Interfaces\GroupRepositoryInterface $group): Podium\Api\PodiumResponse
```

Adds the current user to the group. See [join](group.md#join).

---

### leaveGroup

```
leaveGroup(Podium\Api\Interfaces\GroupRepositoryInterface $group): Podium\Api\PodiumResponse
```

Removes the current user from the group. See [leave](group.md#leave).

---

### log

```
log(string $action, array $data = []): Podium\Api\PodiumResponse
```

Logs the action as the current user. See [log](logger.md#create).

---

### markThread

```
markThread(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Marks the thread for the current user at the post's timestamp. See [mark](thread.md#mark).

---

### removeMessage

```
removeMessage(Podium\Api\Interfaces\MessageRepositoryInterface $message): Podium\Api\PodiumResponse
```

Removes the current user's side of the message. See [remove](message.md#remove).

---

### reviveMessage

```
reviveMessage(Podium\Api\Interfaces\MessageRepositoryInterface $message): Podium\Api\PodiumResponse
```

Revives the current user's side of the message. See [revive](message.md#revive).

---

### sendMessage

```
sendMessage(
    Podium\Api\Interfaces\MemberRepositoryInterface $receiver,
    Podium\Api\Interfaces\MessageRepositoryInterface $replyTo = null,
    array $data = []
): Podium\Api\PodiumResponse
```

Sends a message to the receiver as the current user. See [send](message.md#send).

---

### setPodium

```
setPodium(Podium\Api\Podium $podium): void
```

Sets link to Podium component.

---

### subscribeThread

```
subscribeThread(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Subscribes the current user to the thread. See [subscribe](thread.md#subscribe).

---

### thumbDownPost

```
thumbDownPost(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Gives the post a thumb down from the current user. See [thumbDown](post.md#thumbdown).

---

### thumbResetPost

```
thumbResetPost(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Resets the thumb setting of the current user in the post. See [thumbReset](post.md#thumbreset).

---

### thumbUpPost

```
thumbUpPost(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Gives the post a thumb up from the current user. See [thumbUp](post.md#thumbup).

---

### unsubscribeThread

```
unsubscribeThread(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Unsubscribes the current user from the thread. See [unsubscribe](thread.md#unsubscribe).

---

### votePoll

```
votePoll(Podium\Api\Interfaces\PollPostRepositoryInterface $post, array $answer): Podium\Api\PodiumResponse
```

Votes in the post's poll as the current user. See [votePoll](post.md#votepoll).

---

[<<< Index](../README.md) | [Next >>> Category](category.md)
