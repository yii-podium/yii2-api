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
- [getArchiver](#getArchiver)
- [getBuilder](#getBuilder)
- [getMover](#getMover)
- [getRemover](#getRemover)
- [getRepository](#getRepository)
- [getSorter](#getSorter)
- [move](#move)
- [remove](#remove)
- [replace](#replace)
- [revive](#revive)
- [sort](#sort)


### archive <span id="archive"></span>

```
archive(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Archives the forum. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L224)

#### Events

- `Podium\Api\Services\Forum\ForumArchiver::EVENT_BEFORE_ARCHIVING`
- `Podium\Api\Services\Forum\ForumArchiver::EVENT_AFTER_ARCHIVING`

---

### create <span id="create"></span>

```
create(
    Podium\Api\Interfaces\MemberRepositoryInterface $author,
    Podium\Api\Interfaces\CategoryRepositoryInterface $category,
    array $data = []
): Podium\Api\PodiumResponse
```

Creates a forum as the author under the category. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L95)

#### Events

- `Podium\Api\Services\Forum\ForumBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Forum\ForumBuilder::EVENT_AFTER_CREATING`

---

### edit <span id="edit"></span>

```
edit(Podium\Api\Interfaces\ForumRepositoryInterface $forum, array $data = []): Podium\Api\PodiumResponse
```

Edits the forum. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L108)

#### Events

- `Podium\Api\Services\Forum\ForumBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Forum\ForumBuilder::EVENT_AFTER_EDITING`

---

### getArchiver <span id="getArchiver"></span>

```
getArchiver(): Podium\Api\Interfaces\ArchiverInterface
```

Returns the archiver service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L208)

---

### getBuilder <span id="getBuilder"></span>

```
getBuilder(): Podium\Api\Interfaces\CategorisedBuilderInterface
```

Returns the builder service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L79)

---

### getMover <span id="getMover"></span>

```
getMover(): Podium\Api\Interfaces\MoverInterface
```

Returns the mover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L182)

---

### getRemover <span id="getRemover"></span>

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L118)

---

### getRepository <span id="getRepository"></span>

```
getRepository(): Podium\Api\Interfaces\ForumRepositoryInterface
```

Returns the forum repository. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L63)

---

### getSorter <span id="getSorter"></span>

```
getSorter(): Podium\Api\Interfaces\SorterInterface
```

Returns the sorter service. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L133)

---

### move <span id="move"></span>

```
move(
    Podium\Api\Interfaces\ForumRepositoryInterface $forum,
    Podium\Api\Interfaces\CategoryRepositoryInterface $category
): Podium\Api\PodiumResponse
```

Moves the forum to the category. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L198)

---

### remove <span id="remove"></span>

```
remove(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Removes the forum. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L134)

#### Events

- `Podium\Api\Services\Forum\ForumRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Forum\ForumRemover::EVENT_AFTER_REMOVING`

---

### replace <span id="replace"></span>

```
replace(
    Podium\Api\Interfaces\ForumRepositoryInterface $firstForum,
    Podium\Api\Interfaces\ForumRepositoryInterface $secondForum
): Podium\Api\PodiumResponse
```

Replaces the forums order. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L160)

#### Events

- `Podium\Api\Services\Forum\ForumSorter::EVENT_BEFORE_REPLACING`
- `Podium\Api\Services\Forum\ForumSorter::EVENT_AFTER_REPLACING`

---

### revive <span id="revive"></span>

```
revive(Podium\Api\Interfaces\ForumRepositoryInterface $forum): Podium\Api\PodiumResponse
```

Revives the forum. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L234)

#### Events

- `Podium\Api\Services\Forum\ForumArchiver::EVENT_BEFORE_REVIVING`
- `Podium\Api\Services\Forum\ForumArchiver::EVENT_AFTER_REVIVING`

---

### sort <span id="sort"></span>

```
sort(): Podium\Api\PodiumResponse
```

Sorts the forums order. [[link]](https://github.com/yii-podium/yii2-api/blob/master/src/Components/Forum.php#L172)

#### Events

- `Podium\Api\Services\Forum\ForumSorter::EVENT_BEFORE_SORTING`
- `Podium\Api\Services\Forum\ForumSorter::EVENT_AFTER_SORTING`

---

[Next >>> Group](group.md)
