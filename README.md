# Yii2 Slack integration

Designed to send messages to slack messenger

![How it looks](http://dn.imagy.me/201602/15/12d7dae10bfb96c159f48901d518e196.png)


## Installation

```bash
php composer.phar require understeam/yii2-slack:~0.3 --prefer-dist
```

Also, you should configure [incoming webhook](https://api.slack.com/incoming-webhooks) inside your Slack team.

## Usage

First of all, configure [yiisoft/yii2-httpclient](https://github.com/yiisoft/yii2-httpclient) component:

```php
...
    'components' => [
        'httpclient' => [
            'class' => 'yii\httpclient\Client',
        ],
    ],
...
```

Also you can set it up only inside of slack client:

```php
...
    'components' => [
        'slack' => [
            'httpclient' => [
                'class' => 'yii\httpclient\Client',
            ],
            ...
        ],
    ],
...
```

Configure component:

```php
...
    'components' => [
        'slack' => [
            'class' => 'understeam\slack\Client',
            'url' => '<slack incoming webhook url here>',
            'username' => 'My awesome application',
        ],
    ],
...
```

Now you can send messages right into slack channel via next command:

```php
Yii::$app->slack->send('Hello', ':thumbs_up:', [
    [
        // attachment object
        'text' => 'text of attachment',
        'pretext' => 'pretext here',
    ],
]);
```

To learn more about attachments, [read Slack documentation](https://api.slack.com/incoming-webhooks)

Also you can use slack as a log target:

```php
...
'components' => [
    'log' => [
        'traceLevel' => 3,
        'targets' => [
            [
                'class' => 'understeam\slack\LogTarget',
                'categories' => ['commandBus'],
                'exportInterval' => 1, // Send logs on every message
                'logVars' => [],
            ],
        ],
    ],
],
...
```

