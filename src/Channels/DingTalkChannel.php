<?php
/**
 * User: zhangrongwang
 * Date: 2019/8/7 10:06
 */

namespace Wantp\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Wantp\Notifications\DingTalkClient;
use Wantp\Notifications\Exceptions\DingTalkNotifyFailException;
use Wantp\Notifications\Exceptions\GetAccessTokenFailException;
use Wantp\Notifications\Exceptions\MessageInvalidException;
use Wantp\Notifications\Messages\Message;

class DingTalkChannel
{

    /**
     * The HTTP client instance.
     *
     * @var DingTalkClient
     */
    protected $client;

    /**
     * Create a new Ding Talk channel instance.
     *
     * @param   DingTalkClient  $client
     */
    public function __construct(DingTalkClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param   mixed         $notifiable
     * @param   Notification  $notification
     *
     * @return void
     * @throws MessageInvalidException
     * @throws GetAccessTokenFailException
     * @throws DingTalkNotifyFailException
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$receivers = $notifiable->routeNotificationFor('dingtalk', $notification)) {
            return;
        }

        $this->client->send($this->buildMessage($receivers, $notification->toDingTalk($notifiable)));
    }

    /**
     * 创建消息
     *
     * @param   mixed    $receivers
     * @param   Message  $message
     *
     * @return Message $message
     * @author  zhangrongwang
     * @date    2019-08-07 10:59:37
     */
    protected function buildMessage($receivers, Message $message)
    {

        $this->buildReceivers($receivers, $message);

        return $message;
    }

    /**
     * 创建接收者
     *
     * @param   mixed    $receivers
     * @param   Message  $message
     */
    protected function buildReceivers($receivers, Message $message)
    {

        $receiveUsers       = [];
        $receiveDepartments = [];
        $receiveAll         = false;

        if (is_string($receivers) || is_numeric($receivers)) {
            $receivers = [$receivers];
        }

        foreach ($receivers as $key => $receiver) {
            switch ((string)$key) {
                case "department":
                    $receiveDepartments = $this->mergeReceivers($receiveDepartments, $receiver);
                    break;
                case "all":
                    $receiveAll = is_bool($receiver) ? $receiver : false;
                    break;
                default:
                    $receiveUsers = $this->mergeReceivers($receiveUsers, $receiver);
                    break;
            }
        }

        $message->to($receiveUsers);

        $message->toDept($receiveDepartments);

        $message->toAll($receiveAll);
    }

    /**
     * 合并接收者
     *
     * @param   array  $original
     * @param   mixed  $receiver
     *
     * @return array
     */
    protected function mergeReceivers($original, $receiver)
    {
        $receiver = (is_string($receiver) || is_numeric($receiver)) ? [$receiver] : $receiver;

        return array_merge($original, $receiver);
    }
}