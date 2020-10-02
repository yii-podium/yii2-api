[<<< Index](../README.md)

# Logger

This component provides methods to manage the Podium logs.

## Usage

```
\Yii::$app->podium->logger->...
```

## Configuration

#### builderConfig

Builder service. Expects an instance of [LogBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/LogBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Logger\LoggerBuilder`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Logger\LoggerRemover`.

#### repositoryConfig

Log repository. Expects an instance of [LogRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/LogRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [create](#create)
- [getBuilder](#getBuilder)
- [getRemover](#getRemover)
- [getRepository](#getRepository)
- [remove](#remove)

### create <span id="create"></span>

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    string $action,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a log for action as the member. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Logger.php#L71)

#### Events

- `Podium\Api\Services\Logger\LoggerBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Logger\LoggerBuilder::EVENT_AFTER_CREATING`

---

### getBuilder <span id="getBuilder"></span>

```
getBuilder(): Podium\Api\Interfaces\LogBuilderInterface
```

Returns the builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Logger.php#L57)

---

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Logger.php#L81)

---

### getRepository <span id="getRepository"></span>

```
getRepository(): Podium\Api\Interfaces\LogRepositoryInterface
```

Returns the log repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Logger.php#L41)

---

### remove <span id="remove"></span>

```
remove(Podium\Api\Interfaces\LogRepositoryInterface $log): Podium\Api\PodiumResponse
```

Removes the log. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Logger.php#L95)

#### Events

- `Podium\Api\Services\Logger\LoggerRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Logger\LoggerRemover::EVENT_AFTER_REMOVING`

---

[<<< Index](../README.md) | [Next >>> Member](member.md)
