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
- [getAcquaintance](#getAcquaintance)
- [getAcquaintanceRepository](#getAcquaintanceRepository)
- [getBanisher](#getBanisher)
- [getBuilder](#getBuilder)
- [getMemberRepository](#getMemberRepository)
- [getRemover](#getRemover)
- [ignore](#ignore)
- [register](#register)
- [remove](#remove)
- [unban](#unban)
- [unfriend](#unfriend)
- [unignore](#unignore)

### ban <span id="ban"></span>

```
ban(Podium\Api\Interfaces\MemberRepositoryInterface $member): Podium\Api\PodiumResponse
```

Bans the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L189)

#### Events

- `Podium\Api\Services\Member\MemberBanisher::EVENT_BEFORE_BANNING`
- `Podium\Api\Services\Member\MemberBanisher::EVENT_AFTER_BANNING`

---

### befriend <span id="befriend"></span>

```
befriend(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Befriends the target as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L141)

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_BEFRIENDING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_BEFRIENDING`

---

### edit <span id="edit"></span>

```
edit(Podium\Api\Interfaces\MemberRepositoryInterface $member, array $data = []): Podium\Api\PodiumResponse
```

Edits the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L98)

#### Events

- `Podium\Api\Services\Member\MemberBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Member\MemberBuilder::EVENT_AFTER_EDITING`

---

### getAcquaintance <span id="getAcquaintance"></span>

```
getAcquaintance(): Podium\Api\Interfaces\AcquaintanceInterface
```

Returns the acquaintance service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L108)

---

### getAcquaintanceRepository <span id="getAcquaintanceRepository"></span>

```
getAcquaintanceRepository(): Podium\Api\Interfaces\AcquaintanceRepositoryInterface
```

Returns the acquaintance repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L124)

---

### getBanisher <span id="getBanisher"></span>

```
getBanisher(): Podium\Api\Interfaces\BanisherInterface
```

Returns the banisher service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L175)

---

### getBuilder <span id="getBuilder"></span>

```
getBuilder(): Podium\Api\Interfaces\MemberBuilderInterface
```

Returns the builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L76)

---

### getMemberRepository <span id="getMemberRepository"></span>

```
getMemberRepository(): Podium\Api\Interfaces\MemberRepositoryInterface
```

Returns the member repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L60)

---

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L207)

---

### ignore <span id="ignore"></span>

```
ignore(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Ignores the target as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L157)

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_IGNORING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_IGNORING`

---

### register <span id="register"></span>

```
register($id, array $data = []): Podium\Api\PodiumResponse
```

Registers the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L90)

#### Events

- `Podium\Api\Services\Member\MemberBuilder::EVENT_BEFORE_REGISTERING`
- `Podium\Api\Services\Member\MemberBuilder::EVENT_AFTER_REGISTERING`

---

### remove <span id="remove"></span>

```
remove(Podium\Api\Interfaces\MemberRepositoryInterface $member): Podium\Api\PodiumResponse
```

Removes the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L221)

#### Events

- `Podium\Api\Services\Member\MemberRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Member\MemberRemover::EVENT_AFTER_REMOVING`

---

### unban <span id="unban"></span>

```
unban(Podium\Api\Interfaces\MemberRepositoryInterface $member): Podium\Api\PodiumResponse
```

Unbans the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L197)

#### Events

- `Podium\Api\Services\Member\MemberBanisher::EVENT_BEFORE_UNBANNING`
- `Podium\Api\Services\Member\MemberBanisher::EVENT_AFTER_UNBANNING`

---

### unfriend <span id="unfriend"></span>

```
unfriend(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Unfriends the target as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L149)

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_UNFRIENDING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_UNFRIENDING`

---

### unignore <span id="unignore"></span>

```
unignore(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\MemberRepositoryInterface $target
): Podium\Api\PodiumResponse
```

Unignores the target as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Member.php#L165)

#### Events

- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_BEFORE_UNIGNORING`
- `Podium\Api\Services\Member\MemberAcquaintance::EVENT_AFTER_UNIGNORING`

---

[Next >>> Message](message.md)
