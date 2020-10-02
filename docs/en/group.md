[<<< Index](../README.md)

# Group

This component provides methods to manage the Podium groups.

## Usage

```
\Yii::$app->podium->group->...
```

## Configuration

#### builderConfig

Builder service. Expects an instance of [BuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/BuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Group\GroupBuilder`.

#### keeperConfig

Keeper service. Expects an instance of [KeeperInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/KeeperInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Group\GroupKeeper`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Group\GroupRemover`.

#### repositoryConfig

Group repository. Expects an instance of [GroupRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/GroupRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [create](#create)
- [edit](#edit)
- [getBuilder](#getBuilder)
- [getKeeper](#getKeeper)
- [getRemover](#getRemover)
- [getRepository](#getRepository)
- [join](#join)
- [leave](#leave)
- [remove](#remove)

### create <span id="create"></span>

```
create(array $data = []): Podium\Api\PodiumResponse
```

Creates a group. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L80)

#### Events

- `Podium\Api\Services\Group\GroupBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Group\GroupBuilder::EVENT_AFTER_CREATING`

---

### edit <span id="edit"></span>

```
edit(Podium\Api\Interfaces\GroupRepositoryInterface $group, array $data = []): Podium\Api\PodiumResponse
```

Edits the group. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L90)

#### Events

- `Podium\Api\Services\Group\GroupBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Group\GroupBuilder::EVENT_AFTER_EDITING`

---

### getBuilder <span id="getBuilder"></span>

```
getBuilder(): Podium\Api\Interfaces\BuilderInterface
```

Returns the builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L64)

---

### getKeeper <span id="getKeeper"></span>

```
getKeeper(): Podium\Api\Interfaces\KeeperInterface
```

Returns the keeper service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L126)

---

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L100)

---

### getRepository <span id="getRepository"></span>

```
getRepository(): Podium\Api\Interfaces\GroupRepositoryInterface
```

Returns the group repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L48)

---

### join <span id="join"></span>

```
join(
    Podium\Api\Interfaces\GroupRepositoryInterface $group,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Joins the group as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L142)

#### Events

- `Podium\Api\Services\Group\GroupKeeper::EVENT_BEFORE_JOINING`
- `Podium\Api\Services\Group\GroupKeeper::EVENT_AFTER_JOINING`

---

### leave <span id="leave"></span>

```
leave(
    Podium\Api\Interfaces\GroupRepositoryInterface $group,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Leaves the group as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L152)

#### Events

- `Podium\Api\Services\Group\GroupKeeper::EVENT_BEFORE_LEAVING`
- `Podium\Api\Services\Group\GroupKeeper::EVENT_AFTER_LEAVING`

---

### remove <span id="remove"></span>

```
remove(Podium\Api\Interfaces\GroupRepositoryInterface $group): Podium\Api\PodiumResponse
```

Removes the group. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Group.php#L116)

#### Events

- `Podium\Api\Services\Group\GroupRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Group\GroupRemover::EVENT_AFTER_REMOVING`

---

[Next >>> Logger](logger.md)
