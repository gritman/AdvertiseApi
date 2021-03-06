<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/21
 * Time: 16:09
 */
namespace Api\Service;

use Api\Service;
use Api\Common\Util;

class AdHeadlineService extends AdService
{
    public function __construct()
    {
        parent::__construct();
        $this->adSdkId = '1003';
    }

    protected function fillSubGameData($cacheData, &$gameData)
    {
        $gameData['active_at'] = intval($_GET['active_at'] / 1000);
    }

    protected function setConfig($appConfig, $ideaConfig)
    {
      $this->adConfig['app_key'] = $appConfig->app_key;
        $this->adConfig['callback_url'] = $appConfig->callback_url;
    }

    // 游戏激活时送来的IDFA IMEI MAC都是没有MD5的
    public function genMidFromOpen($appType, $deviceId, $mac)
    {
        if ($appType == '1') {
            return $deviceId;
        } else { // 0或其他
            $md5Imei = md5($deviceId);
            $md5Mac = md5($mac);
            return $md5Imei;//.'_'.$md5Mac;
        }
    }

    // 头条的点击，IOS传的是IDFA，没有md5；其他传的是IMEI和MAC，已经MD5
    public function genMidFromClick($appType, $deviceId, $mac)
    {
        if ($appType == '1') {
            return $deviceId;
        } else {
            return $deviceId;//.'_'.$mac;
        }
    }

    public function activedReturn($gameData, $cacheData)
    {
        // $appKey = "945948789875";
        // $url = "http://ad.toutiao.com/track/activate/?callback=12334_334_434_2323_4334_2343_www.gdt.com&muid=KHK-SD-DFK&os=1&source=td&conv_time=1463414400";
        $url = "http://ad.toutiao.com/track/activate/?callback="
            . $cacheData['cacheValue']
            . "&muid=" . $gameData['mid']
            . "&os=" . $gameData['app_type']
            . "&conv_time=" . $gameData['active_at'];
        $signature = base64_encode(hash_hmac("SHA1", (string)$this->adConfig['app_key'], (string)$url, true));
        $url = $url . "&signature=" . $signature;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($ch);
        Util::log("AdHeadline activedReturn $url $result", $gameData['mid']);
        $resultArray = json_decode($result, true);
        $retval = ['active_url' => $url, 'active_ret' => $result, 'ret' => $resultArray['ret']];
        return $retval;
        //        return $resultArray['ret'];
    }
}




