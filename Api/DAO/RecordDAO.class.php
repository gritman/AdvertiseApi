<?php
  /**
   * Created by PhpStorm.
   * User: Administrator
   * Date: 2017/3/21
   * Time: 17:24
   */

namespace Api\DAO;

use Api\Common\Util;
use Api\Model\AdModel;

class RecordDAO
{
  protected $activedModel;
  protected $clickModel;
  protected $openModel;

  static public $openStatusEnum = [
				   'ACTIVATED' => 1,
				   'NO_CACHED' => 0
				   ];

  public function __construct()
  {
    $this->activedModel = new AdModel('active');
    $this->clickModel = new AdModel('click');
    $this->openModel = new AdModel('open');
  }

  public function addActiveModel($gameData, $thirdRet)
  {
    $strval = serialize($thirdRet);
    Util::log("addActivatedModel::: $strval", $gameData['mid']);
    $ret = $thirdRet['ret'];
    $activeUrl = $thirdRet['active_url'];
    $activeRet = $thirdRet['active_ret'];
    $activatedData = [
		      'mid' => Util::valueOrNull('mid', $gameData),
		      'appid' => Util::valueOrNull('appid', $gameData),
		      'flag' => $ret,
		      'sdkid' => Util::valueOrNull('ad_id', $gameData),
		      'ip' => Util::valueOrNull('ip', $gameData),
		      'active_url' => $activeUrl,
		      'active_ret' => $activeRet,
		      'open_count' => 1,
		      'ad_appid' => Util::valueOrNull('ad_app_id', $gameData),
		      ];
    $result = $this->activedModel->add($activatedData);
    if ($result) {
      return Util::retJson(0, 'add activated  model success');
    } else {
      return Util::retJson(5, 'add activated model failure');
    }
  }

  public function addClickModel($clickData)
  {
    $dbData = [
	       'mid' => Util::valueOrNull('mid', $clickData),
	       'click_time' => Util::valueOrNull('click_time', $clickData),
	       'appid' => Util::valueOrNull('appid', $clickData),
	       'clickid' => Util::valueOrNull('clickid', $clickData),
	       'app_type' => Util::valueOrNull('app_type', $clickData),
	       'advertise_id' => Util::valueOrNull('advertise_id', $clickData),
	       'sdkid' => Util::valueOrNull('sdkid', $clickData),
	       'cid' => Util::valueOrNull('cid', $clickData),
	       'callback' => Util::valueOrNull('callback', $clickData),
	       'click_count' => 1
	       ];
    $result = $this->clickModel->add($dbData);
    if ($result) {
      return Util::retJson(0, 'add click  model success');
    } else {
      return Util::retJson(1, 'add click model failure');
    }
  }

  public function addOpenModel($gameData, $openStatus)
  {
    $dbData = [
	       'mid' => Util::valueOrNull('mid', $gameData),
	       'appid' => Util::valueOrNull('appid', $gameData),
	       'app_type' => Util::valueOrNull('app_type', $gameData),
	       'active_at' => Util::valueOrNull('active_at', $gameData),
	       'app_val' => Util::valueOrNull('app_val', $gameData),
	       'actived_time' => Util::valueOrNull('actived_time', $gameData),
	       'ad_appid' => Util::valueOrNull('ad_app_id', $gameData),
	       'app_key' => Util::valueOrNull('app_key', $gameData),
	       'advertise_id' => Util::valueOrNull('advertise_id', $gameData),
	       'ad_id' => Util::valueOrNull('ad_id', $gameData),
	       'ip' => Util::valueOrNull('ip', $gameData),
	       'mac' => Util::valueOrNull('mac', $gameData),
	       'sdkid' => Util::valueOrNull('sdkid', $gameData),
	       'device_number' => Util::valueOrNull('device_id', $gameData),
	       'channel' => Util::valueOrNull('channel_id', $gameData),
	       'src_id' => Util::valueOrNull('src_id', $gameData),
	       'status' => $openStatus
	       ];
    $result = $this->openModel->add($dbData);
    if ($result) {
      return Util::retJson(0, 'add open data ok');
    } else {
      return Util::retJson(1, 'add open data fail');
    }
  }
}