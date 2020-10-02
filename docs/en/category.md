[<<< Index](../README.md)

# Category

This component provides methods to manage the Podium categories.

## Usage

```
\Yii::$app->podium->category->...
```

## Configuration

#### builderConfig

Builder service. Expects an instance of 
[CategoryBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CategoryBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategoryBuilder`.

#### sorterConfig

Sorter service. Expects an instance of [SorterInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/SorterInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategorySorter`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategoryRemover`.

#### archiverConfig

Archiver service. Expects an instance of [ArchiverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ArchiverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategoryArchiver`.

#### repositoryConfig

Category repository. Expects an instance of 
[CategoryRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CategoryRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [archive](#archive)
- [create](#create)
- [edit](#edit)
- [getArchiver](#getArchiver)
- [getBuilder](#getBuilder)
- [getRemover](#getRemover)
- [getRepository](#getRepository)
- [getSorter](#getSorter)
- [remove](#remove)
- [replace](#replace)
- [revive](#revive)
- [sort](#sort)

### archive <span id="archive"></span>

```
archive(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Archives the category. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L187)

#### Events

- `Podium\Api\Services\Category\CategoryArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Category\CategoryArchiver::EVENT_AFTER_ARCHIVING`

### create <span id="create"></span>

```
create(Podium\Api\Interfaces\MemberRepositoryInterface $author, array $data = []): Podium\Api\PodiumResponse
```

Creates a category as the author. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L87)

#### Events

- `Podium\Api\Services\Category\CategoryBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Category\CategoryBuilder::EVENT_AFTER_CREATING`

### edit <span id="edit"></span>

```
edit(Podium\Api\Interfaces\CategoryRepositoryInterface $category, array $data = []): Podium\Api\PodiumResponse
```

Edits the category. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L97)

#### Events

- `Podium\Api\Services\Category\CategoryBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Category\CategoryBuilder::EVENT_AFTER_EDITING`

### getArchiver <span id="getArchiver"></span>

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L171)

### getBuilder <span id="getBuilder"></span>

```
getBuilder(): Podium\Api\Interfaces\CategoryBuilderInterface
```

Returns the builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L71)

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L107)

### getRepository <span id="getRepository"></span>

```
getRepository(): Podium\Api\Interfaces\CategoryRepositoryInterface
```

Returns the category repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L55)

### getSorter <span id="getSorter"></span>

```
getSorter(): Podium\Api\Interfaces\SorterInterface
```

Returns the sorter service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L133)

### remove <span id="remove"></span>

```
remove(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Removes the category. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L123)

#### Events

- `Podium\Api\Services\Category\CategoryRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Category\CategoryRemover::EVENT_AFTER_REMOVING`

### replace <span id="replace"></span>

```
replace(
    Podium\Api\Interfaces\CategoryRepositoryInterface $firstCategory,
    Podium\Api\Interfaces\CategoryRepositoryInterface $secondCategory
): Podium\Api\PodiumResponse
```

Replaces the categories order. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L149)

#### Events

- `Podium\Api\Services\Category\CategorySorter::EVENT_BEFORE_REPLACING`
- `Podium\Api\Services\Category\CategorySorter::EVENT_AFTER_REPLACING`

### revive <span id="revive"></span>

```
revive(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Revives the category. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L197)

#### Events

- `Podium\Api\Services\Category\CategoryArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Category\CategoryArchiver::EVENT_AFTER_REVIVING`

### sort <span id="sort"></span>

```
sort(): Podium\Api\PodiumResponse
```

Sorts the categories order. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Category.php#L161)

#### Events

- `Podium\Api\Services\Category\CategorySorter::EVENT_BEFORE_SORTING`
- `Podium\Api\Services\Category\CategorySorter::EVENT_AFTER_SORTING`

--

[Next >>> Forum](forum.md)
