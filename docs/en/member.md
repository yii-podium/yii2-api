[<<< Index](../README.md)

# Member

This component provides methods to manage the Podium members.

## Usage

```
\Yii::$app->podium->member->...
```

## Configuration

#### acquaintanceConfig

Acquaintance service. Expects an instance of [AcquaintanceInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/AcquaintanceInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Member\MemberAcquaintance`.

#### acquaintanceRepositoryConfig

Acquaintance repository. Expects an instance of [AcquaintanceRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/AcquaintanceRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### banisherConfig

Banisher service. Expects an instance of [BanisherInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/BanisherInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Member\MemberBanisher`.

#### builderConfig

Builder service. Expects an instance of [MemberBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MemberBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Member\MemberBuilder`.

#### memberRepositoryConfig

Member repository. Expects an instance of [MemberRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MemberRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Member\MemberRemover`.

## Methods

- [ban](#ban)
- [befriend](#befriend)
- [edit](#edit)
- [getAcquaintance](#getacquaintance)
- [getAcquaintanceRepository](#getacquaintancerepository)
- [getBanisher](#getbanisher)
- [getBuilder](#getbuilder)
- [getMemberRepository](#getmemberrepository)
- [getRemover](#getremover)
- [ignore](#ignore)
- [register](#register)
- [remove](#remove)
- [unban](#unban)
- [unfriend](#unfriend)
- [unignore](#unignore)

### ban

```
ban(Podium\Api\Interfaces\MemberRepositoryInterface $member): Podium\Api\PodiumResponse
```

Bans the member. The banned member cannot use Podium. See also [unban](#unban).

#### Events

- `Podium\Api\Services\Member\MemberBanisher::EVENT_BEFORE_BANNING`
- `Podium\Api\Services\Member\MemberBanisher::EVENT_AFTER_BANNING`

---

### befriend

```
befriend(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Befriends the target as the member if the target is not befriended already (can be ignored though). See also [unfriend](#unfriend), 
[ignore](#ignore), and [unignore](#unignore).

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_BEFRIENDING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_BEFRIENDING`

---

### edit

```
edit(Podium\Api\Interfaces\MemberRepositoryInterface $member, array $data = []): Podium\Api\PodiumResponse
```

Edits the member. See also [register](#register).

Optional data:
- `username`
- `slug`

#### Events

- `Podium\Api\Services\Member\MemberBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Member\MemberBuilder::EVENT_AFTER_EDITING`

---

### getAcquaintance

```
getAcquaintance(): Podium\Api\Interfaces\AcquaintanceInterface
```

Returns the acquaintance service which handles [befriending](#befriend), [unfriending](#unfriend), [ignoring](#ignore), 
and [unignoring](#unignore).

---

### getAcquaintanceRepository

```
getAcquaintanceRepository(): Podium\Api\Interfaces\AcquaintanceRepositoryInterface
```

Returns the acquaintance repository.

---

### getBanisher

```
getBanisher(): Podium\Api\Interfaces\BanisherInterface
```

Returns the banisher service which handles [banning](#ban) and [unbanning](#unban).

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\MemberBuilderInterface
```

Returns the builder service which handles [registering](#register) and [editing](#edit).

---

### getMemberRepository

```
getMemberRepository(): Podium\Api\Interfaces\MemberRepositoryInterface
```

Returns the member repository.

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### ignore

```
ignore(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Ignores the target as the member if the target is not ignored already (can be befriended though). Member cannot send 
messages to the member that ignores him. See also [befriend](#unfriend), [unfriend](#unfriend), and [unignore](#unignore).

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_IGNORING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_IGNORING`

---

### register

```
register($id, array $data = []): Podium\Api\PodiumResponse
```

Registers the member. See also [edit](#edit).

Required data:
- `username`

Optional data (with defaults):
- `slug` (generated from `username`)

#### Events

- `Podium\Api\Services\Member\MemberBuilder::EVENT_BEFORE_REGISTERING`
- `Podium\Api\Services\Member\MemberBuilder::EVENT_AFTER_REGISTERING`

---

### remove

```
remove(Podium\Api\Interfaces\MemberRepositoryInterface $member): Podium\Api\PodiumResponse
```

Removes the member.

#### Events

- `Podium\Api\Services\Member\MemberRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Member\MemberRemover::EVENT_AFTER_REMOVING`

---

### unban

```
unban(Podium\Api\Interfaces\MemberRepositoryInterface $member): Podium\Api\PodiumResponse
```

Unbans the member. See also [ban](#ban).

#### Events

- `Podium\Api\Services\Member\MemberBanisher::EVENT_BEFORE_UNBANNING`
- `Podium\Api\Services\Member\MemberBanisher::EVENT_AFTER_UNBANNING`

---

### unfriend

```
unfriend(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Unfriends the target as the member if the target is befriended. See also [befriend](#befriend), [ignore](#ignore), 
and [unignore](#unignore).

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_UNFRIENDING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_UNFRIENDING`

---

### unignore

```
unignore(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Unignores the target as the member if the target is ignored. See also [befriend](#befriend), [unfriend](#unfriend), 
and [ignore](#ignore).

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_UNIGNORING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_UNIGNORING`

---

[<<< Index](../README.md) | [Next >>> Message](message.md)
