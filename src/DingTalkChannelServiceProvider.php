<?php
/**
 * User: zhangrongwang
 * Date: 2019/8/7 10:01
 */

namespace Wantp\Notifications;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class DingTalkChannelServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([realpath(__DIR__.'/../config/dingtalk.php') => config_path('dingtalk.php')]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/dingtalk.php'), 'dingtalk');
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('dingtalk', function ($app) {
                $client = new DingTalkClient(
                    new HttpClient,
                    $this->app['config']['dingtalk.host'],
                    $this->app['config']['dingtalk.app_key'],
                    $this->app['config']['dingtalk.app_secret'],
                    $this->app['config']['dingtalk.uri_list']
                );
                $client->setAgentId($this->app['config']['dingtalk.agent_id']);

                return new Channels\DingTalkChannel($client);
            });
        });
    }
}