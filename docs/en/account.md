[<<< Index](../README.md)

# Account

This component provides handy shortcut to other components that are using 
[MemberRepositoryInterface](https://github.com/yii-podium/yii2-api/blob/master/src/Interfaces/MemberRepositoryInterface.php) 
and allows to call their methods from the perspective of a logged-in user.

## Usage

```php
\Yii::$app->podium->account->method();
```

## Methods

- [getMembership](#account-getMembership)

### getMembership <span id="account-getMembership"></span>

```php
getMembership(bool $renew = false): MemberRepositoryInterface
```

Returns member's repository loaded with current user's data.

--

[Next >>> Category](category.md)
