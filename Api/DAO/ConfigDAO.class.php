<?php
  /**
   * Created by PhpStorm.
   * User: Administrator
   * Date: 2017/3/21
   * Time: 11:27
   */

namespace Api\DAO;

use Api\Common\Util;
use Api\Exception\AdFatalException;
use Api\Model\AdModel;

class ConfigDAO
{
  protected $appConfigModel;
  protected $ideaConfigModel;
  protected $tplModel;

  public function __construct()
  {
    $this->appConfigModel = new AdModel('app_config');
    $this->ideaConfigModel = new AdModel('app_idea_config');
    $this->tplModel = new AdModel('config_tpl');
  }

  public function getConfigList($appId, $osType) {
    $queryCondition = array('app_id' => $appId, 'os_type' => $osType, 'status' => 1);
    $configList = $this->ideaConfigModel->where($queryCondition)->select();
    $result = array();
    foreach($configList as $config) {
      $adId = $config['ad_id'];
      $ideaId = $config['ad_appid'];
      array_push($result, array('adId' => $adId, 'ideaId' => $ideaId));
    }
    return $result;
  }

  public function getAdConfig($appId, $adId) {
    $queryCondition = array('app_id' => $appId, 'ad_id' => $adId);
    $appConfig = $this->appConfigModel->where($queryCondition)->find();
    if (!$appConfig) {
      throw new AdFatalException("cannot getAdConfig [appid, ad_id]: $appId $adId");
    }
    $ret = json_decode($appConfig['config'])[0];
    Util::log("getAdConfig: ".json_encode($appConfig['config']), "");
    return $ret;
  }

  public function getIdeaConfig($appId, $adId, $ideaId) {
    $queryCondition = array('app_id' => $appId, 'ad_id' => $adId, 'ad_appid' => $ideaId, 'status' => 1);
    $ideaConfig = $this->ideaConfigModel->where($queryCondition)->find();
    if (!$ideaConfig) {
      throw new AdFatalException("cannot getIdeaConfig [appid, ad_id, ad_appid]: $appId $adId $ideaId");
    }
    $ret = json_decode($ideaConfig['config'])[0];
    Util::log("getIdeaConfig: ".json_encode($ideaConfig['config']), "");
    return $ret;
  }


}