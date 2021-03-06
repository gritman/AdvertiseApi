<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/21
 * Time: 16:09
 */
namespace Api\Service;

use Api\Service\AdService;

class AdWeiboService extends AdService
{
    public function __construct()
    {
        parent::__construct();
        $this->adSdkId = '1006';
    }

    protected function fillSubGameData($cacheData, &$gameData)
    {
        $gameData['callback'] = $cacheData['cacheValue'];
    }

    protected function setConfig($appConfig, $ideaConfig)
    {
    }

    public function genMidFromOpen($appType, $deviceId, $mac)
    {
        return md5($deviceId);
    }

    // 点击时，第三方传过来的IMEI和IDFA已经md5过了
    public function genMidFromClick($appType, $deviceId, $mac)
    {
        return md5($deviceId);
    }

    public function activedReturn($gameData, $cacheData)
    {
        $callback = $cacheData['callback'];
        $url = urldecode($callback);
        $retJson = file_get_contents($url);
        $retArr = json_decode($retJson, true);
        AdService::log("AdvertisingController activedReturn url: $url , ret: $retJson", $arr['mid']);
        $retVal = ['active_url' => (string)$url, 'active_ret' => '0', 'ret' => 'ret'];
        return $retVal;
    }
}



