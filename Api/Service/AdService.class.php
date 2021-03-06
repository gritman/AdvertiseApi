<?php
  /**
   * Created by PhpStorm.
   * User: Administrator
   * Date: 2017/2/21
   * Time: 16:09
   */

namespace Api\Service;

use Api\DAO\CacheDAO;
use Api\DAO\ConfigDAO;
use Api\DAO\RecordDAO;
use Api\Exception\AdFatalException;
use Api\Common\Util;

abstract class AdService
{

  abstract protected function genMidFromClick($appType, $deviceId, $mac);

  abstract protected function genMidFromOpen($appType, $deviceId, $mac);

  abstract public function activedReturn($gameData, $cacheData);

  abstract protected function setConfig($appConfig, $ideaConfig);

  abstract protected function fillSubGameData($cacheData, &$gameData);

  protected $configDAO;
  protected $cacheDAO;
  protected $recordDAO;
  protected $adId = null;
  protected $adName = null;

  protected $adConfig = array();
  protected $ideaConfig = array();

  protected function __construct()
  {
    $this->recordDAO = new RecordDAO();
    $this->configDAO = new ConfigDAO();
    $this->cacheDAO = new CacheDAO();
  }

  static public function factory($adName)
  {
    try {
      Util::log("AdService factory $adName", "");
      $className = Util::adNameToServiceName($adName);
      $serviceReflectionObj = new \ReflectionClass($className);
      $serviceObj = $serviceReflectionObj->newInstance(); 
      $serviceObj->adName = $adName;
      $serviceObj->adId = AdService::getAdIdByAdName($adName);
      return $serviceObj;
    } catch (\Exception $e) {
      throw new AdFatalException("cannot factory AdService [adName]: " . $adName . " with ori exception: " . $e->getMessage());
    }
  }



  static private $AdNameToAdIdArray = array(
					    'Advertising' => '1001',// 广点通
					    'AdHeadline' => '1003',// 头条
					    'AdWeibo' => '1006',// 微博
					    'AdUnity' => '1007'// 畅思unity
					    );

  static public function getAdNameByAdId($adId) {
    return array_search($adId, AdService::$AdNameToAdIdArray);
  }

  static public function getAdIdByAdName($adName)
  {
    if (array_key_exists($adName, AdService::$AdNameToAdIdArray)) {
      return AdService::$AdNameToAdIdArray[$adName];
    } else {
      return false;
    }
  }

  static public function genCacheKey($sdkId, $ideaId, $mid)
  {
    $cacheKey = 'puma' . CacheDAO::$separator . (string)($sdkId) . CacheDAO::$separator . (string)($ideaId) . CacheDAO::$separator . (string)($mid);
    return $cacheKey;
  }

  public function genCacheVal($callback)
  {
    $cacheVal = $callback;
    return $cacheVal;
  }

  public function cacheAdsClicks($clickData)
  {
    $cacheKey = $this->genCacheKey($this->adId, $clickData['appid'], $clickData['mid']);
    $cacheValue = $this->genCacheVal($clickData['callback']);
    if ($this->cacheDAO->isCached($cacheKey)) {
      $this->cacheDAO->setCacheKeyValue($cacheKey, $cacheValue);
      $this->recordDAO->addClickModel($clickData);
      Util::jsonEchoExit(0, 'click already cached, overwrite');
    } else {
      $this->cacheDAO->setCacheKeyValue($cacheKey, $cacheValue);
      $this->recordDAO->addClickModel($clickData);
      Util::jsonEchoExit(0, 'click cached');
    }
  }

  public function addActiveModel($gameData, $thirdRet)
  {
    $this->activeDAO->addActiveModel($gameData, $thirdRet);
  }

  public function addClickModel($clickData)
  {
    $this->activeDAO->addClickModel($clickData);
  }

  public function addOpenModel($gameData, $openStatus)
  {
    $this->recordDAO->addOpenModel($gameData, $openStatus);
  }

  public function initConfig($appId, $adId, $ideaId)
  {
    $appConfig = $this->configDAO->getAdConfig($appId, $adId);
    $ideaConfig = $this->configDAO->getIdeaConfig($appId, $adId, $ideaId);
    foreach($appConfig as $k=>$v) {
      Util::log("debug $k $v", "");
    }
    foreach($ideaConfig as $k=>$v) {
      Util::log("debug $k $v", "");
    }
    Util::log("initConfig app: ".serialize($appConfig)." ".gettype($appConfig), "");
    Util::log("initConfig idea: ".serialize($ideaConfig)." ".gettype($ideaConfig), "");
    $this->setConfig($appConfig, $ideaConfig);
  }

  public function fillGameData($cacheData, &$gameData)
  {
    $gameData['mid'] = $this->genMidFromOpen($gameData['app_type'], $gameData['device_id'], $gameData['mac']);
    $gameData['ad_app_id'] = $cacheData['ideaId'];
    $gameData['ad_id'] = $cacheData['adId'];
    $gameData['active_at'] = intval($_GET['active_at']);
    $this->fillSubGameData($cacheData, $gameData);
  }
}




