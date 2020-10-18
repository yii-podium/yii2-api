[<<< Index](../README.md)

# Log

This component provides methods to manage the Podium logs.

## Usage

```
\Yii::$app->podium->log->...
```

## Configuration

#### builderConfig

Builder service. Expects an instance of [LogBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/LogBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Logger\LogBuilder`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Logger\LogRemover`.

#### repositoryConfig

Log repository. Expects an instance of [LogRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/LogRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [create](#create)
- [getBuilder](#getbuilder)
- [getRemover](#getremover)
- [getRepository](#getrepository)
- [remove](#remove)

### create

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    string $action,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a log for action as the member.

#### Events

- `Podium\Api\Services\Logger\LogBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Logger\LogBuilder::EVENT_AFTER_CREATING`

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\LogBuilderInterface
```

Returns the builder service which handles [creating](#create).

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getRepository

```
getRepository(): Podium\Api\Interfaces\LogRepositoryInterface
```

Returns the log repository.

---

### remove

```
remove(Podium\Api\Interfaces\LogRepositoryInterface $log): Podium\Api\PodiumResponse
```

Removes the log.

#### Events

- `Podium\Api\Services\Logger\LogRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Logger\LogRemover::EVENT_AFTER_REMOVING`

---

[<<< Index](../README.md) | [Next >>> Member](member.md)
