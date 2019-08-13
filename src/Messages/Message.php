<?php
/**
 * User: zhangrongwang
 * Date: 2019/8/7 10:07
 */

namespace Wantp\Notifications\Messages;


abstract class Message
{
    /**
     * @var array 接收者id列表
     */
    protected $userIdList = [];

    /**
     * @var array 接收部门id列表
     */
    protected $deptIdList = [];

    /**
     * @var bool 是否发送全员消息
     */
    protected $toAllUser = false;

    /**
     * @var string 消息类型
     */
    protected $msgType = 'markdown';

    /**
     * @var string 消息链接
     */
    protected $msgUrl = '';

    /**
     * @var string 消息图片链接
     */
    protected $msgPicUrl = '';

    /**
     * @var string 消息标题
     */
    protected $title = '';

    /**
     * @var string 消息内容
     */
    protected $body = '';

    /**
     * 设置消息接收者
     *
     * @param   array  $userIdList
     *
     * @return $this
     */
    public function to(array $userIdList)
    {
        $this->userIdList = $userIdList;

        return $this;
    }

    /**
     * 设置消息接收部门
     *
     * @param   array  $deptIdList
     *
     * @return self $this
     */
    public function toDept(array $deptIdList)
    {
        $this->deptIdList = $deptIdList;

        return $this;
    }

    /**
     * 设置是否全员发送
     *
     * @param   bool  $toAllUser
     *
     * @return self $this
     */
    public function toAll(bool $toAllUser)
    {
        $this->toAllUser = $toAllUser;

        return $this;
    }

    /**
     * 设置消息类型为markdown
     *
     * @return self $this
     */
    public function markdown()
    {
        $this->msgType = 'markdown';

        return $this;
    }

    /**
     * 设置消息类型为链接
     *
     * @return self $this
     */
    public function link()
    {
        $this->msgType = 'link';

        return $this;
    }

    /**
     * 设置消息标题
     *
     * @param $title
     *
     * @return self $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * 设置消息内容
     *
     * @param $body
     *
     * @return self $this
     */
    public function body($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * 设置消息链接（类型为link时有效）
     *
     * @param $msgUrl
     *
     * @return self $this
     */
    public function msgUrl($msgUrl)
    {
        $this->msgUrl = $msgUrl;

        return $this;
    }

    /**
     * 设置消息图片链接（类型为link时有效）
     *
     * @param $msgPicUrl
     *
     * @return self $this
     */
    public function msgPicUrl($msgPicUrl)
    {
        $this->msgPicUrl = $msgPicUrl;

        return $this;
    }

    /**
     * 获取接收者列表
     *
     * @return array
     */
    public function getUserIdList()
    {
        return $this->userIdList;
    }

    /**
     * 获取接收部门列表
     *
     * @return array
     */
    public function getDeptIdList()
    {
        return $this->deptIdList;
    }

    /**
     * 获取是否全员发送
     *
     * @return bool
     */
    public function getToAllUser()
    {
        return $this->toAllUser;
    }

    /**
     * 获取消息
     *
     * @return array
     */
    abstract public function getMsg();

    /**
     * 生成markdown类型消息
     *
     * @return array
     */
    protected function buildMarkdownMsg()
    {
        return [
            'msgtype'  => 'markdown',
            'markdown' => [
                'title' => $this->title,
                'text'  =>
                    '## ' . $this->title .
                    PHP_EOL . PHP_EOL . '###### ' . date('Y-m-d H:i:s', time()) .
                    PHP_EOL . PHP_EOL . $this->body
            ]
        ];
    }

    /**
     * 生成link类型消息
     *
     * @return array
     */
    protected function buildLinkMsg()
    {
        return [
            'msgtype' => 'link',
            'link'    => [
                'messageUrl' => $this->msgUrl,
                'picUrl'     => $this->msgPicUrl,
                'title'      => $this->title,
                'text'       => $this->body
            ]
        ];
    }


}