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

Expects an instance of [MemberRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MemberRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### userConfig

Expects an instance of [User](https://github.com/yiisoft/yii2/blob/master/framework/web/User.php) or component's ID or 
configuration array that can be resolved as the above. Default: `user`.

## Methods

- [archiveMessage](#archiveMessage)
- [befriendMember](#befriendMember)
- [createCategory](#createCategory)
- [createForum](#createForum)
- [createPost](#createPost)
- [createThread](#createThread)
- [edit](#edit)
- [getMembership](#getMembership)
- [getPodium](#getPodium)
- [ignoreMember](#ignoreMember)
- [joinGroup](#joinGroup)
- [leaveGroup](#leaveGroup)
- [log](#log)
- [markThread](#markThread)
- [removeMessage](#removeMessage)
- [reviveMessage](#reviveMessage)
- [sendMessage](#sendMessage)
- [setPodium](#setPodium)
- [subscribeThread](#subscribeThread)
- [thumbDownPost](#thumbDownPost)
- [thumbResetPost](#thumbResetPost)
- [thumbUpPost](#thumbUpPost)
- [unfriendMember](#unfriendMember)
- [unignoreMember](#unignoreMember)
- [unsubscribeThread](#unsubscribeThread)
- [votePoll](#votePoll)

### archiveMessage <span id="archiveMessage"></span>

```
archiveMessage(Podium\Api\Interfaces\MessageRepositoryInterface $message): Podium\Api\PodiumResponse
```

Archives the current user's side of the message. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L321)

### befriendMember <span id="befriendMember"></span>

```
befriendMember(Podium\Api\Interfaces\MemberRepositoryInterface $target): Podium\Api\PodiumResponse
```

Befriends the target member as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L252)

### createCategory <span id="createCategory"></span>

```
createCategory(array $data = []): Podium\Api\PodiumResponse
```

Creates a category as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L119)

### createForum <span id="createForum"></span>

```
createForum(Podium\Api\Interfaces\CategoryRepositoryInterface $parentCategory, array $data = []): Podium\Api\PodiumResponse
```

Creates a forum under the parent category as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L130)

### createPost <span id="createPost"></span>

```
createPost(Podium\Api\Interfaces\ThreadRepositoryInterface $parentThread, array $data = []): Podium\Api\PodiumResponse
```

Creates a post under the parent thread as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L152)

### createThread <span id="createThread"></span>

```
createThread(Podium\Api\Interfaces\ForumRepositoryInterface $parentForum, array $data = []): Podium\Api\PodiumResponse
```

Creates a thread under the parent forum as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L141)

### edit <span id="edit"></span>

```
edit(array $data = []): Podium\Api\PodiumResponse
```

Edits the current user data. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L241)

### getMembership <span id="getMembership"></span>

```
getMembership(bool $renew = false): Podium\Api\Interfaces\MemberRepositoryInterface
```

Returns member's repository loaded with current user's data. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L72)

### getPodium <span id="getPodium"></span>

```
getPodium(): Podium\Api\Module
```

Returns Podium module. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L57)

### ignoreMember <span id="ignoreMember"></span>

```
ignoreMember(Podium\Api\Interfaces\MemberRepositoryInterface $target): Podium\Api\PodiumResponse
```

Ignores the target member as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L274)

### joinGroup <span id="joinGroup"></span>

```
joinGroup(Podium\Api\Interfaces\GroupRepositoryInterface $group): Podium\Api\PodiumResponse
```

Adds the current user to the group. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L97)

### leaveGroup <span id="leaveGroup"></span>

```
leaveGroup(Podium\Api\Interfaces\GroupRepositoryInterface $group): Podium\Api\PodiumResponse
```

Removes the current user from the group. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L108)

### log <span id="log"></span>

```
log(string $action, array $data = []): Podium\Api\PodiumResponse
```

Logs the action as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L343)

### markThread <span id="markThread"></span>

```
markThread(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Marks the thread for the current user at the post's timestamp. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L163)

### removeMessage <span id="removeMessage"></span>

```
removeMessage(Podium\Api\Interfaces\MessageRepositoryInterface $message): Podium\Api\PodiumResponse
```

Removes the current user's side of the message. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L310)

### reviveMessage <span id="reviveMessage"></span>

```
reviveMessage(Podium\Api\Interfaces\MessageRepositoryInterface $message): Podium\Api\PodiumResponse
```

Revives the current user's side of the message. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L332)

### sendMessage <span id="sendMessage"></span>

```
sendMessage(
    Podium\Api\Interfaces\MemberRepositoryInterface $receiver,
    Podium\Api\Interfaces\MessageRepositoryInterface $replyTo = null,
    array $data = []
): Podium\Api\PodiumResponse
```

Sends a message to the receiver as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L296)

### setPodium <span id="setPodium"></span>

```
setPodium(Podium\Api\Module $podium): void
```

Sets Podium module's link. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L49)

### subscribeThread <span id="subscribeThread"></span>

```
subscribeThread(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Subscribes the current user to the thread. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L175)

### thumbDownPost <span id="thumbDownPost"></span>

```
thumbDownPost(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Gives the post a thumb down from the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L208)

### thumbResetPost <span id="thumbResetPost"></span>

```
thumbResetPost(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Resets the thumb setting of the current user in the post. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L219)

### thumbUpPost <span id="thumbUpPost"></span>

```
thumbUpPost(Podium\Api\Interfaces\PostRepositoryInterface $post): Podium\Api\PodiumResponse
```

Gives the post a thumb up from the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L175)

### unfriendMember <span id="unfriendMember"></span>

```
unfriendMember(Podium\Api\Interfaces\MemberRepositoryInterface $target): Podium\Api\PodiumResponse
```

Unfriends the target member as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L263)

### unignoreMember <span id="unignoreMember"></span>

```
unignoreMember(Podium\Api\Interfaces\MemberRepositoryInterface $target): Podium\Api\PodiumResponse
```

Unignores the target member as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L285)

### unsubscribeThread <span id="unsubscribeThread"></span>

```
unsubscribeThread(Podium\Api\Interfaces\ThreadRepositoryInterface $thread): Podium\Api\PodiumResponse
```

Unsubscribes the current user from the thread. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L186)

### votePoll <span id="votePoll"></span>

```
votePoll(Podium\Api\Interfaces\PollPostRepositoryInterface $post, array $answer): Podium\Api\PodiumResponse
```

Votes in the post's poll as the current user. 
[[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Account.php#L230)

--

[Next >>> Category](category.md)
