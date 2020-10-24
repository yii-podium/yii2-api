[<<< Index](../README.md)

# Permit

This component provides methods to manage the Podium roles and repository access.

## Usage

```
\Yii::$app->podium->permit->...
```

## Configuration

#### builderConfig

Role builder service. Expects an instance of [BuilderInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/BuilderInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Permit\RoleBuilder`.

#### checkerConfig

Permit checker service. Expects an instance of [CheckerInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/CheckerInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Permit\PermitChecker`.

#### granterConfig

Role granter service. Expects an instance of [GranterInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/GranterInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Permit\RoleGranter`.

#### removerConfig

Role remover service. Expects an instance of [RemoverInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RemoverInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `Podium\Api\Services\Permit\RoleRemover`.

#### repositoryConfig

Role repository. Expects an instance of [RoleRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/RoleRepositoryInterface.php) 
or component's ID or configuration array that can be resolved as the above. Default: `null`.

## Methods

- [check](#check)
- [createRole](#createrole)
- [editRole](#editrole)
- [getBuilder](#getbuilder)
- [getChecker](#getchecker)
- [getGranter](#getgranter)
- [getRemover](#getremover)
- [getRepository](#getrepository)
- [grantRole](#grantrole)
- [removeRole](#removerole)
- [revokeRole](#revokerole)

### check

```
public function check(
    Podium\Api\Interfaces\DeciderInterface $decider,
    string $type,
    Podium\Api\Interfaces\RepositoryInterface $subject = null,
    Podium\Api\Interfaces\MemberRepositoryInterface $member = null
): Podium\Api\PodiumDecision
```

Checks the member's permit by the type for accessing the subject. Default types are `create` (Podium\Api\Enums\PermitType::CREATE), 
`read` (Podium\Api\Enums\PermitType::READ), `update` (Podium\Api\Enums\PermitType::UPDATE), and `delete` (Podium\Api\Enums\PermitType::DELETE). 
The actual service, that is deciding whether the member can get access to the subject, is the decider implementing DeciderInterface. 
By default, API is not checking any access.

#### Events

- `Podium\Api\Services\Permit\PermitChecker::EVENT_BEFORE_CHECKING`
- `Podium\Api\Services\Permit\PermitChecker::EVENT_AFTER_CHECKING`

---

### createRole

```
createRole(array $data): Podium\Api\PodiumResponse
```

Creates a role. See also [editRole](#editrole).

#### Events

- `Podium\Api\Services\Permit\RoleBuilder::EVENT_BEFORE_CREATING`
- `Podium\Api\Services\Permit\RoleBuilder::EVENT_AFTER_CREATING`

---

### editRole

```
editRole(Podium\Api\Interfaces\RoleRepositoryInterface $role, array $data = []): Podium\Api\PodiumResponse
```

Edits the role. See also [createRole](#createrole).

#### Events

- `Podium\Api\Services\Permit\RoleBuilder::EVENT_BEFORE_EDITING`
- `Podium\Api\Services\Permit\RoleBuilder::EVENT_AFTER_EDITING`

---

### getBuilder

```
getBuilder(): Podium\Api\Interfaces\BuilderInterface
```

Returns the builder service which handles [creating](#createrole) and [editing](#editrole) roles.

---

### getChecker

```
getChecker(): Podium\Api\Interfaces\CheckerInterface
```

Returns the checker service which handles [checking](#check) permits.

---

### getGranter

```
getGranter(): Podium\Api\Interfaces\GranterInterface
```

Returns the granter service which handles [granting](#grantrole) and [revoking](#revokerole) roles.

---

### getRemover

```
getRemover(): Podium\Api\Interfaces\RemoverInterface
```

Returns the remover service which handles [removing](#removerole) roles.

---

### getRepository

```
getRepository(): Podium\Api\Interfaces\RoleRepositoryInterface
```

Returns the role repository.

---

### grantRole

```
grantRole(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\RoleRepositoryInterface $role
): Podium\Api\PodiumResponse
```

Grants the role to the member. See also [revokeRole](#revokerole).

#### Events

- `Podium\Api\Services\Permit\RoleGranter::EVENT_BEFORE_GRANTING`
- `Podium\Api\Services\Permit\RoleGranter::EVENT_AFTER_GRANTING`

---

### removeRole

```
removeRole(Podium\Api\Interfaces\RoleRepositoryInterface $role): Podium\Api\PodiumResponse
```

Removes the role.

#### Events

- `Podium\Api\Services\Permit\RoleRemover::EVENT_BEFORE_REMOVING`
- `Podium\Api\Services\Permit\RoleRemover::EVENT_AFTER_REMOVING`

---

### revokeRole

```
revokeRole(
    Podium\Api\Interfaces\MemberRepositoryInterface $member,
    Podium\Api\Interfaces\RoleRepositoryInterface $role
): Podium\Api\PodiumResponse
```

Revokes the role from the member. See also [grantRole](#grantrole).

#### Events

- `Podium\Api\Services\Permit\RoleGranter::EVENT_BEFORE_REVOKING`
- `Podium\Api\Services\Permit\RoleGranter::EVENT_AFTER_REVOKING`

---

[<<< Index](../README.md) | [Next >>> Post](post.md)
