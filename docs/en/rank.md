[<<< Index](../README.md)

# Rank

This component provides methods to manage the Podium ranks.

## Usage

```
\Yii::$app->podium->rank->...
```

## Configuration

#### builderConfig

Builder service. Expects an instance of [BuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/BuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Rank\RankBuilder`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Rank\RankRemover`.

#### repositoryConfig

Rank repository. Expects an instance of [GroupRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RankRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [create](#create)
- [edit](#edit)
- [getBuilder](#getbuilder)
- [getRemover](#getremover)
- [getRepository](#getrepository)
- [remove](#remove)

### create

```
create(array $data = []): Podium\Api\PodiumResponse
```

Creates a rank. See also [edit](#edit).

#### Events

- `Podium\Api\Services\Rank\RankBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Rank\RankBuilder::EVENT_AFTER_CREATING`

---

### edit

```
edit(Podium\Api\Interfaces\RankRepositoryInterface $rank, array $data = []): Podium\Api\PodiumResponse
```

Edits the rank. See also [create](#create).

#### Events

- `Podium\Api\Services\Rank\RankBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Rank\RankBuilder::EVENT_AFTER_EDITING`

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\BuilderInterface
```

Returns the builder service which handles [creating](#create) and [editing](#edit).

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getRepository

```
getRepository(): Podium\Api\Interfaces\RankRepositoryInterface
```

Returns the rank repository.

---

### remove

```
remove(Podium\Api\Interfaces\RankRepositoryInterface $rank): Podium\Api\PodiumResponse
```

Removes the rank.

#### Events

- `Podium\Api\Services\Rank\RankRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Rank\RankRemover::EVENT_AFTER_REMOVING`

---

[<<< Index](../README.md) | [Next >>> Thread](thread.md)
