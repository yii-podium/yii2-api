[<<< Index](../README.md)

# Installation

When Podium is already in your `vendor` folder, simply add this to your 
[application configuration](https://www.yiiframework.com/doc/guide/2.0/en/concept-configurations#application-configurations) 
file:

```php
[
    'modules' => [
        'podium' => \Podium\Api\Module::class,
    ],
]
```

In this case the _module ID_ is `podium`. All application's modules must use unique IDs, so if yours is already using 
`podium`, or you would like to choose another word, you can do so, but notice that this documentation is always assuming 
the default ID.

# Usage

To use any of Podium components simply call it with module's ID:

```php
\Yii::$app->podium->componentName->componentMethod();
```

Every component's method is returning [Podium\Api\PodiumResponse](../../src/PodiumResponse.php) object.

--
[Next >>> Account](account.md)