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
- [getBuilder](#getbuilder)
- [getKeeper](#getkeeper)
- [getRemover](#getremover)
- [getRepository](#getrepository)
- [join](#join)
- [leave](#leave)
- [remove](#remove)

### create

```
create(array $data = []): Podium\Api\PodiumResponse
```

Creates a group. See also [edit](#edit).

#### Events

- `Podium\Api\Services\Group\GroupBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Group\GroupBuilder::EVENT_AFTER_CREATING`

---

### edit

```
edit(Podium\Api\Interfaces\GroupRepositoryInterface $group, array $data = []): Podium\Api\PodiumResponse
```

Edits the group. See also [create](#create).

#### Events

- `Podium\Api\Services\Group\GroupBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Group\GroupBuilder::EVENT_AFTER_EDITING`

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\BuilderInterface
```

Returns the builder service which handles [creating](#create) and [editing](#edit).

---

### getKeeper

```
getKeeper(): Podium\Api\Interfaces\KeeperInterface
```

Returns the keeper service which handles [joining](#join) and [leaving](#leave) the group.

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getRepository

```
getRepository(): Podium\Api\Interfaces\GroupRepositoryInterface
```

Returns the group repository.

---

### join

```
join(
    Podium\Api\Interfaces\GroupRepositoryInterface $group,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Joins the group as the member. See also [leave](#leave).

#### Events

- `Podium\Api\Services\Group\GroupKeeper::EVENT_BEFORE_JOINING`
- `Podium\Api\Services\Group\GroupKeeper::EVENT_AFTER_JOINING`

---

### leave

```
leave(
    Podium\Api\Interfaces\GroupRepositoryInterface $group,
    Podium\Api\Interfaces\MemberRepositoryInterface $member
): Podium\Api\PodiumResponse
```

Leaves the group as the member. See also [join](#join).

#### Events

- `Podium\Api\Services\Group\GroupKeeper::EVENT_BEFORE_LEAVING`
- `Podium\Api\Services\Group\GroupKeeper::EVENT_AFTER_LEAVING`

---

### remove

```
remove(Podium\Api\Interfaces\GroupRepositoryInterface $group): Podium\Api\PodiumResponse
```

Removes the group.

#### Events

- `Podium\Api\Services\Group\GroupRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Group\GroupRemover::EVENT_AFTER_REMOVING`

---

[<<< Index](../README.md) | [Next >>> Logger](logger.md)
