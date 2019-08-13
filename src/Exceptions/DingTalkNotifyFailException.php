<?php
/**
 * User: zhangrongwang
 * Date: 2019/8/7 10:58
 */

namespace Wantp\Notifications\Exceptions;


use Throwable;

class DingTalkNotifyFailException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}