<?php
/**
 * User: wangliugang
 * Date: 2017/3/16
 * Time: 9:35
 * Email: wangliugang@bianfeng.com
 */

namespace Api\Service;

class AdUnityService extends AdService
{
    public function __construct()
    {
        parent::__construct();
        $this->adSdkId = '1007';
    }

    protected function fillSubGameData($cacheData, &$gameData)
    {
        $gameData['callback'] = $cacheData['cacheValue'];
    }

    protected function setConfig($appConfig, $ideaConfig)
    {
    }

    public function genMidFromClick($appType, $deviceId, $mac)
    {
        return $deviceId;
    }

    public function genMidFromOpen($appType, $deviceId, $mac)
    {
        return $deviceId;
    }

    public function activedReturn($gameData, $cacheData)
    {
        $callback = $cacheData['callback'];

        $url = urldecode($callback);

        $retJson = file_get_contents($url);
        $retArr = json_decode($retJson, true);
        AdService::log("AdvertisingController activedReturn url: $url , ret: $retJson", $gameData['mid']);
        $retVal = ['active_url' => (string)$url, 'active_ret' => '0', 'ret' => $retJson];
        return $retVal;
    }
}