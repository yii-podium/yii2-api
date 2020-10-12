[<<< Index](../README.md)

# Forum

This component provides methods to manage the Podium forums.

## Usage

```
\Yii::$app->podium->forum->...
```

## Configuration

#### archiverConfig

Archiver service. Expects an instance of [ArchiverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ArchiverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Forum\ForumArchiver`.

#### builderConfig

Builder service. Expects an instance of [CategorisedBuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CategorisedBuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Forum\ForumBuilder`.

#### hiderConfig

Hider service. Expects an instance of [HiderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/HiderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Forum\ForumHider`.

#### moverConfig

Mover service. Expects an instance of [MoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Forum\ForumMover`.

#### removerConfig

Remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Forum\ForumRemover`.

#### repositoryConfig

Forum repository. Expects an instance of [ForumRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/ForumRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

#### sorterConfig

Sorter service. Expects an instance of [SorterInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/SorterInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Forum\ForumSorter`.

## Methods

- [archive](#archive)
- [create](#create)
- [edit](#edit)
- [getArchiver](#getarchiver)
- [getBuilder](#getbuilder)
- [getHider](#gethider)
- [getMover](#getmover)
- [getRemover](#getremover)
- [getRepository](#getrepository)
- [getSorter](#getsorter)
- [hide](#hide)
- [move](#move)
- [remove](#remove)
- [replace](#replace)
- [reveal](#reveal)
- [revive](#revive)
- [sort](#sort)

### archive

```
archive(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Archives the forum. Only archived forums can be removed. Archiving a forum does not archive its child threads and posts. 
See also [revive](#revive).

#### Events

- `Podium\Api\Services\Forum\ForumArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Forum\ForumArchiver::EVENT_AFTER_ARCHIVING`

---

### create

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    Podium\Api\Interfaces\CategoryRepositoryInterface $category,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a forum as the author under the category. See also [edit](#edit).

Required data:
- `name`

Optional data (with defaults):
- `description` (`null`)
- `slug` (generated from `name`)
- `sort` (next value after the highest sort order available)

#### Events

- `Podium\Api\Services\Forum\ForumBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Forum\ForumBuilder::EVENT_AFTER_CREATING`

---

### edit

```
edit(Podium\Api\Interfaces\ForumRepositoryInterface $forum, array $data = []): Podium\Api\PodiumResponse
```

Edits the forum. See also [create](#create).

Optional data:
- `name`
- `description`
- `slug`
- `sort`

#### Events

- `Podium\Api\Services\Forum\ForumBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Forum\ForumBuilder::EVENT_AFTER_EDITING`

---

### getArchiver

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service which handles [archiving](#archive) and [reviving](#revive).

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\CategorisedBuilderInterface
```

Returns the builder service which handles [creating](#create) and [editing](#edit).

---

### getHider

```
getHider(): Podium\Api\Interfaces\Hiderface
```

Returns the hider service which handles [hiding](#hide) and [revealing](#reveal).

---

### getMover

```
getMover(): Podium\Api\Interfaces\MoverInterface
```

Returns the mover service which handles [moving](#move) a forum between categories.

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#remove).

---

### getRepository

```
getRepository(): Podium\Api\Interfaces\ForumRepositoryInterface
```

Returns the forum repository.

---

### getSorter

```
getSorter(): Podium\Api\Interfaces\SorterInterface
```

Returns the sorter service which handles [replacing](#replace) and [sorting](#sort) the forums order.

---

### hide

```
hide(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Hides the forum. Forum can be hidden from certain groups of users. See also [reveal](#reveal).

#### Events

- `Podium\Api\Services\Forum\ForumHider::EVENT_BEFORE_HIDING`
- `Podium\Api\Services\Forum\ForumHider::EVENT_AFTER_HIDING`

---

### move

```
move(
    Podium\Api\Interfaces\ForumRepositoryInterface $forum,
    Podium\Api\Interfaces\CategoryRepositoryInterface $category
): Podium\Api\PodiumResponse
```

Moves the forum to the category.


#### Events

- `Podium\Api\Services\Forum\ForumMover::EVENT_BEFORE_MOVING`
- `Podium\Api\Services\Forum\ForumMover::EVENT_AFTER_MOVING`

---

### remove

```
remove(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Removes the forum. Only archived forums can be removed. Removing a forum removes all its child threads and posts, 
regardless of their archived status.

#### Events

- `Podium\Api\Services\Forum\ForumRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Forum\ForumRemover::EVENT_AFTER_REMOVING`

---

### replace

```
replace(
    Podium\Api\Interfaces\ForumRepositoryInterface $firstForum,
    Podium\Api\Interfaces\ForumRepositoryInterface $secondForum
): Podium\Api\PodiumResponse
```

Replaces the forums order. Because both forums can have the same order the resulting order can be the same. See also 
[sort](#sort).

#### Events

- `Podium\Api\Services\Forum\ForumSorter::EVENT_BEFORE_REPLACING`
- `Podium\Api\Services\Forum\ForumSorter::EVENT_AFTER_REPLACING`

---

### reveal

```
reveal(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Reveals the forum. Forum that is not hidden (default state) is available for all groups of users. See also [hide](#hide).

#### Events

- `Podium\Api\Services\Forum\ForumHider::EVENT_BEFORE_REVEALING`
- `Podium\Api\Services\Forum\ForumHider::EVENT_AFTER_REVEALING`

---

### revive

```
revive(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Revives the forum. The revived forum is no longer archived. It does not affect the archived status of its child threads 
and posts. See also [archive](#archive).

#### Events

- `Podium\Api\Services\Forum\ForumArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Forum\ForumArchiver::EVENT_AFTER_REVIVING`

---

### sort

```
sort(): Podium\Api\PodiumResponse
```

Sorts the forums order. Sorting makes sure two or more forums are not having the same order value anymore. See also 
[replace](#replace).

#### Events

- `Podium\Api\Services\Forum\ForumSorter::EVENT_BEFORE_SORTING`
- `Podium\Api\Services\Forum\ForumSorter::EVENT_AFTER_SORTING`

---

[<<< Index](../README.md) | [Next >>> Group](group.md)
