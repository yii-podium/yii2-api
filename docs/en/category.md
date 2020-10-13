[<<< Index](../README.md)

# Category

This component provides methods to manage the Podium categories.

## Usage

```
\Yii::$app->podium->category->...
```

## Configuration

#### archiverConfig

Archiver service. Expects an instance of [ArchiverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ArchiverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategoryArchiver`.

#### builderConfig

Builder service. Expects an instance of [CategoryBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CategoryBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategoryBuilder`.

#### hiderConfig

Hider service. Expects an instance of [HiderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/HiderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategoryHider`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategoryRemover`.

#### repositoryConfig

Category repository. Expects an instance of [CategoryRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CategoryRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### sorterConfig

Sorter service. Expects an instance of [SorterInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/SorterInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Category\CategorySorter`.

## Methods

- [archive](#archive)
- [create](#create)
- [edit](#edit)
- [getArchiver](#getarchiver)
- [getBuilder](#getbuilder)
- [getHider](#gethider)
- [getRemover](#getremover)
- [getRepository](#getrepository)
- [getSorter](#getsorter)
- [hide](#hide)
- [remove](#remove)
- [replace](#replace)
- [reveal](#reveal)
- [revive](#revive)
- [sort](#sort)

### archive

```
archive(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Archives the category. Only archived categories can be removed. Archiving a category does not archive its child forums, 
threads, and posts. See also [revive](#revive).

#### Events

- `Podium\Api\Services\Category\CategoryArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Category\CategoryArchiver::EVENT_AFTER_ARCHIVING`

---

### create

```
create(Podium\Api\Interfaces\MemberRepositoryInterface $author, array $data = []): Podium\Api\PodiumResponse
```

Creates a category as the author. See also [edit](#edit).

#### Events

- `Podium\Api\Services\Category\CategoryBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Category\CategoryBuilder::EVENT_AFTER_CREATING`

---

### edit

```
edit(Podium\Api\Interfaces\CategoryRepositoryInterface $category, array $data = []): Podium\Api\PodiumResponse
```

Edits the category. See also [create](#create).

#### Events

- `Podium\Api\Services\Category\CategoryBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Category\CategoryBuilder::EVENT_AFTER_EDITING`

---

### getArchiver

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service which handles [archiving](#archive) and [reviving](#revive).

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\CategoryBuilderInterface
```

Returns the builder service which handles [creating](#create) and [editing](#edit).

---

### getHider

```
getHider(): Podium\Api\Interfaces\Hiderface
```

Returns the hider service which handles [hiding](#hide) and [revealing](#reveal).

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getRepository

```
getRepository(): Podium\Api\Interfaces\CategoryRepositoryInterface
```

Returns the category repository.

---

### getSorter

```
getSorter(): Podium\Api\Interfaces\SorterInterface
```

Returns the sorter service which handles [replacing](#replace) and [sorting](#sort) the categories order.

---

### hide

```
hide(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Hides the category. Category can be hidden from certain groups of users. See also [reveal](#reveal).

#### Events

- `Podium\Api\Services\Category\CategoryHider::EVENT_BEFORE_HIDING`
- `Podium\Api\Services\Category\CategoryHider::EVENT_AFTER_HIDING`

---

### remove

```
remove(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Removes the category. Only archived categories can be removed. Removing a category removes all its child forums, threads, 
and posts, regardless of their archived status.

#### Events

- `Podium\Api\Services\Category\CategoryRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Category\CategoryRemover::EVENT_AFTER_REMOVING`

---

### replace

```
replace(
    Podium\Api\Interfaces\CategoryRepositoryInterface $firstCategory,
    Podium\Api\Interfaces\CategoryRepositoryInterface $secondCategory
): Podium\Api\PodiumResponse
```

Replaces the categories order. Because both categories can have the same order the resulting order can be the same. See 
also [sort](#sort).

#### Events

- `Podium\Api\Services\Category\CategorySorter::EVENT_BEFORE_REPLACING`
- `Podium\Api\Services\Category\CategorySorter::EVENT_AFTER_REPLACING`

---

### reveal

```
reveal(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Reveals the category. Category that is not hidden (default state) is available for all groups of users. See also [hide](#hide).

#### Events

- `Podium\Api\Services\Category\CategoryHider::EVENT_BEFORE_REVEALING`
- `Podium\Api\Services\Category\CategoryHider::EVENT_AFTER_REVEALING`

---

### revive

```
revive(Podium\Api\Interfaces\CategoryRepositoryInterface $category): Podium\Api\PodiumResponse
```

Revives the category. The revived category is no longer archived. It does not affect the archived status of its child 
forums, threads, and posts. See also [archive](#archive).

#### Events

- `Podium\Api\Services\Category\CategoryArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Category\CategoryArchiver::EVENT_AFTER_REVIVING`

---

### sort

```
sort(): Podium\Api\PodiumResponse
```

Sorts the categories order. Sorting makes sure two or more categories are not having the same order value anymore. See 
also [replace](#replace).

#### Events

- `Podium\Api\Services\Category\CategorySorter::EVENT_BEFORE_SORTING`
- `Podium\Api\Services\Category\CategorySorter::EVENT_AFTER_SORTING`

---

[<<< Index](../README.md) | [Next >>> Forum](forum.md)
