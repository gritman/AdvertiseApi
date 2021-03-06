<?php
  /**
   * Created by PhpStorm.
   * User: Administrator
   * Date: 2017/2/21
   * Time: 16:09
   */
namespace Api\Service;

use Api\Service\AdService;
use Api\Common\Util;
class AdvertisingService extends AdService
{
  protected $conv_type = 'MOBILEAPP_ACTIVITE';
  public function __construct()
  {
    parent::__construct();
    $this->adSdkId = '1001';
  }

  protected function fillSubGameData($cacheData, &$gameData)
  {
    $gameData['active_at'] = intval($_GET['active_at'] / 1000);
  }

  protected function setConfig($appConfig, $ideaConfig)
  {
    $this->adConfig['app_key'] = $appConfig->app_key;
    Util::log("setConfig app_key: ".$appConfig->app_key, "");
    $this->adConfig['callback_param'] = $appConfig->callback_param;
    $this->adConfig['advertise_id'] = $appConfig->advertise_id;
    Util::log("setConfig: ".serialize($this->adConfig), "");
  }

  public function genMidFromOpen($appType, $deviceId, $mac)
  {
    if ($appType == '1') {
      $strEncode = strtoupper($deviceId);
      return md5($strEncode);
    } else { // 0或其他
      $strEncode = strtolower($deviceId);
      return md5($strEncode);
    }
  }

  // 点击时，第三方传过来的IMEI和IDFA已经md5过了
  public function genMidFromClick($appType, $deviceId, $mac)
  {
    if ($appType == '1') {
      return $deviceId;
    } else {
      return $deviceId;
    }
  }

  public function activedReturn($gameData, $cacheData)
  {
    $appid = $gameData['ad_app_id'];
    $encrypt_key = $this->adConfig['app_key'];
    $sign_key = $this->adConfig['callback_param'];
    if ($gameData['app_type'] == '0') {
      if ($gameData['appid'] == '1097' || $gameData['appid'] == '1024') {
	$app_type = 'unionandroid';
      } else {
	$app_type = 'android';
      }
    } else {
      $app_type = 'ios';
    }
    $conv_type = $this->conv_type;
    $advertiser_id = $this->adConfig['advertise_id'];
    $muid = $gameData['mid'];
    $conv_time = $gameData['active_at'];
    $client_ip = $gameData['ip'];
    $query_string = 'muid=' . urlencode($muid) . '&conv_time=' . urlencode($conv_time) . '&client_ip=' . urlencode($client_ip);;
    $url = "http://t.gdt.qq.com/conv/app/" . $appid . "/conv?" . $query_string;
    $encode_page = urlencode($url);
    $property = $sign_key . '&GET&' . $encode_page;
    $signature = md5($property);
    $base_data = $query_string . "&sign=" . urlencode($signature);
    $xorData = $this->simpleXor($base_data, $encrypt_key);
    $data = base64_encode($xorData);

    $attachment = "conv_type=" . urlencode($conv_type) . "&app_type=" . urlencode($app_type) . "&advertiser_id=" . urlencode($advertiser_id);

    $url = "http://t.gdt.qq.com/conv/app/" . $appid . "/conv?v=" . urlencode($data) . "&" . $attachment;
    $gdtReturnJson = file_get_contents($url);
    $gdtReturn = json_decode($gdtReturnJson, true);
    Util::log("AdvertisingController activedReturn url: $url , ret: $gdtReturnJson", $gameData['mid']);
    $retVal = ['active_url' => (string)$url, 'active_ret' => (string)json_encode($gdtReturnJson), 'ret' => $gdtReturn['ret']];
    return $retVal;
  }

  private function simpleXor($str, $key)
  {
    $txt = '';
    $keylen = strlen($key);
    for ($i = 0; $i < strlen($str); $i++) {
      $k = $i % $keylen;
      $txt .= $str[$i] ^ $key[$k];
    }
    return $txt;
  }

}



