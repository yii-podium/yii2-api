[<<< Index](../README.md)

# Installation

When Podium is already in your `vendor` folder, simply add this to your 
[application configuration](https://www.yiiframework.com/doc/guide/2.0/en/concept-configurations#application-configurations) 
file:

```php
[
    'components' => [
        'podium' => \Podium\Api\Podium::class,
    ],
]
```

In this case the _component ID_ is `podium`. All application's components must use unique IDs, so if yours is already using 
`podium`, or you would like to choose another word, you can do so, but notice that this documentation is always assuming 
the default ID.

# Usage

To use any of Podium components simply call it with ID chosen above like:

```php
\Yii::$app->podium->componentName->componentMethod();
```

Most of component's methods are returning [PodiumResponse](https://github.com/yii-podium/yii2-api/blob/master/src/PodiumResponse.php) 
object.

---

[<<< Index](../README.md) | [Next >>> Account](account.md)
