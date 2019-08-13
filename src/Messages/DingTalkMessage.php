<?php
/**
 * User: zhangrongwang
 * Date: 2019/8/7 10:07
 */

namespace Wantp\Notifications\Messages;


class DingTalkMessage extends Message
{

    /**
     * 获取消息
     *
     * @return array
     */
    public function getMsg()
    {
        switch ($this->msgType) {
            case 'link':
                return $this->buildLinkMsg();
                break;
            default:
                return $this->buildMarkdownMsg();
                break;
        }
    }



}