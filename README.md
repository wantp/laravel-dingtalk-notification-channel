# Laravel-dingtalk-notification-channel 
Laravel消息通知的钉钉通知扩展包

### Require
- Laravel > 5.8 
- PHP > 7.1

### Install
```shell script
$ composer require wantp/laravel-dingtalk-notification-channel "~1.0"
```

### Usage
##### 发布配置文件,会发布配置文件app/dingtalk.php
```shell script
$ php artisan vendor:publish
```

##### 钉钉通知配置  
.env
```
DING_TALK_APP_KEY=your_dingtalk_app_key
DING_TALK_APP_SECRET=your_dingtalk_app_secret
DING_TALK_APP_NOTIFICATION_AGENT_ID=your_dingtalk_agent_id
```

##### 格式化钉钉通知

如果要支持钉钉通知，那么你需要在通知类上定义一个via和一个toDingTalk 方法。via方法返回通知channel，
toDingTalk方法接收一个 $notifiable 实体并返回 Wantp\Notifications\Messages\DingTalkMessage 实例。

app/Notifications/YourNotification.php
```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Wantp\Notifications\Messages\DingTalkMessage;
use Wantp\Notifications\Messages\Message;

class YourNotification extends Notification
{
    use Queueable;

    public function via()
    {
        return ['dingtalk'];
    }

    public function toDingTalk($notifiable)
    {
        return (new DingTalkMessage())
            ->title('研发部测试通知')
            ->body('研发部测试通知，收到请忽略');

    }
}

```

##### 钉钉通知路由
使用 dingtalk 通道发送通知的时候，需要在实体上定义一个 routeNotificationForDingTalk 方法，
该方法返回钉钉的user_id。  
以下示例是DingTalkUser模型记录了钉钉的user_id，User模型关联DingTalkUser并通过
routeNotificationForDingTalk方法返回user对应的钉钉user_id，这只是一个示例，你可以定义自己的实现。
```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notification;

class User extends Authenticatable
{
    use Notifiable;

    ...

    public function dingTalkUser()
    {
        return $this->hasOne(DingTalkUser::class);
    }

    public function routeNotificationForDingTalk(Notification $notification)
    {
        return $this->dingTalkUser->ding_talk_user_id;
    }
}

```

##### 发送钉钉通知
```php

$users = User::find(1);

$users->notify(new YourNotificationNotification());
```

##### 发送钉钉通知给多个用户
```php
$users = User::take(2)->get();

Notification::send($users, new ApplicationApproveNotification());

```

##### 自定义发送通知路由,可发送钉钉通知给不在系统中的用户
```php
Notification::route('dingtalk', ['user_id_1', 'user_id_2'])->notify(new ApplicationApproveNotification());
```

##### link类型通知
内置了markdown(默认)和link类型的通知，使用link方法定义link类型通知
app/Notifications/YourNotification.php
```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Wantp\Notifications\Messages\DingTalkMessage;
use Wantp\Notifications\Messages\Message;

class YourNotification extends Notification
{
    use Queueable;

    public function via()
    {
        return ['dingtalk'];
    }

    public function toDingTalk($notifiable)
    {
        return (new DingTalkMessage())
            ->link()
            ->title('研发部测试通知')
            ->body('研发部测试通知，收到请忽略')
            ->msgUrl('http://laravel.test/messages/1')
            ->msgPicUrl('http://laravel.test/message/pictures/1');

    }
}
```

dingtalk channel和其他channel的使用几乎没有什么不同，其他用法请参考[Laravel文档](https://laravel.com/docs/5.8/notifications)


### License

The code for laravel-dingtalk-notification-channel is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).