<?php
/**
 * User: zhangrongwang
 * Date: 2019/8/7 11:14
 */

namespace Wantp\Notifications;

use GuzzleHttp\Client as HttpClient;
use Wantp\Notifications\Exceptions\DingTalkNotifyFailException;
use Wantp\Notifications\Exceptions\GetAccessTokenFailException;
use Wantp\Notifications\Exceptions\MessageInvalidException;
use Wantp\Notifications\Messages\Message;

class DingTalkClient
{
    const SUCCESS_CODE = 0;

    /**
     * The HTTP client instance.
     *
     * @var HttpClient
     */
    protected $http;

    /**
     * @var string 钉钉host
     */
    protected $host;

    /**
     * @var string 钉钉appkey
     */
    protected $appKey;

    /**
     * @var string 钉钉appsecret
     */
    protected $appSecret;

    /**
     * @var string|int 应用id
     */
    protected $agentId;

    /**
     * @var array uri列表
     */
    protected $uriList;

    /**
     * @var null|string access_token
     */
    protected $accessToken = null;

    /**
     * @var int 有效期
     */
    protected $accessTokenExpiryDuration = 7200;

    /**
     * @var int 超时时间
     */
    protected $accessTokenExpiryTime;

    /**
     * DingTalkClient constructor.
     *
     * @param   HttpClient  $http
     * @param               $host
     * @param               $appKey
     * @param               $appSecret
     * @param               $uriList
     */
    public function __construct(HttpClient $http, $host, $appKey, $appSecret, $uriList)
    {
        $this->http      = $http;
        $this->host      = $host;
        $this->appKey    = $appKey;
        $this->appSecret = $appSecret;
        $this->uriList   = $uriList;
    }

    /**
     * Notes:setAgentId 设置agent_id
     *
     * @param $agentId
     *
     * @author  zhangrongwang
     * @date    2019-08-07 11:53:24
     */
    public function setAgentId($agentId)
    {
        $this->agentId = $agentId;
    }

    /**
     * Notes:getAccessToken 获取access_token
     *
     * @throws GetAccessTokenFailException
     * @author  zhangrongwang
     * @date    2019-08-07 11:52:35
     */
    public function getAccessToken()
    {
        if (!$this->accessToken || ($this->accessToken && time() >= $this->accessTokenExpiryTime)) {
            $response = $this->http->get($this->host . $this->uriList['get_access_token'] . '?appkey=' . $this->appKey
                . '&appsecret=' . $this->appSecret);

            $responseBody = json_decode($response->getBody(), true);

            if (!isset($responseBody['errcode']) || 0 != $responseBody['errcode']) {
                throw new GetAccessTokenFailException($responseBody['errmsg'] ?? 'Get access token fail');
            }
            $this->accessToken           = $responseBody['access_token'];
            $this->accessTokenExpiryTime = time() + $this->accessTokenExpiryDuration;
        }

        return $this->accessToken;
    }

    /**
     * Notes:send 发送通知
     *
     * @param $msg
     *
     * @return array
     * @throws GetAccessTokenFailException
     * @throws MessageInvalidException
     * @throws DingTalkNotifyFailException
     * @author  zhangrongwang
     * @date    2019-08-07 11:52:41
     */
    public function send(Message $msg)
    {
        $uri = $this->host . $this->uriList['notify'] . '?access_token=' . $this->getAccessToken();

        $postData = ['agent_id' => $this->agentId];

        $receiverCorrect = false;

        if ($toAllUser = $msg->getToAllUser()) {
            $receiverCorrect         = true;
            $postData['to_all_user'] = $toAllUser;
        }

        if (!empty($userIdList = $msg->getUserIdList())) {
            $receiverCorrect         = true;
            $postData['userid_list'] = join(',', $userIdList);
        }


        if (!empty($departmentIdList = $msg->getDeptIdList())) {
            $receiverCorrect          = true;
            $postData['dept_id_list'] = join(',', $departmentIdList);
        }

        if (!$receiverCorrect) {
            throw new MessageInvalidException('userid_list,dept_id_list,to_all_user 必须设置一个');
        }

        $postData['msg'] = $msg->getMsg();

        $response = $this->http->post($uri, ['json' => $postData]);

        $responseBody = json_decode($response->getBody(), true);

        if (!isset($responseBody['errcode']) || self::SUCCESS_CODE != $responseBody['errcode']) {
            throw new DingTalkNotifyFailException($responseBody['errmsg'] ?? 'Get access token fail');
        }

        return $responseBody;
    }

}