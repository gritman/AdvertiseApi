<?php
  /**
   * Created by PhpStorm.
   * User: Administrator
   * Date: 2017/2/21
   * Time: 16:23
   */

namespace Api\Controller;

use Api\Service\AdService;
use Api\Exception\AdFatalException;
use Think\Controller;
use Api\Common\Util;

abstract class AdController extends Controller
{
  abstract public function predealAdsClickToClickData();

  protected $adId = null;
  protected $adName = null;
  protected $adService = null;

  public function __construct()
  {
    parent::__construct();
  }

  protected function init($controllerName)
  {
    Util::log("AdController init controllername: $controllerName", "");
    $this->adName = Util::controllerNameToAdName($controllerName);
    $this->adId = AdService::getAdIdByAdName($this->adName);
    $this->adService = AdService::factory($this->adName);
    if($this->adService != null) {
      Util::log("create adservice ok: ".get_class($this->adService), "");
    } else {
      Util::log("create adservice fail", "");
    }
    if(!$this->adService) {
      throw new AdFatalException(sprintf("cannot refect controller [controllerName: %s]", $controllerName));
    }
  }

  public function adsClick()
  {
    $clickData = $this->predealAdsClickToClickData();
    if(!array_key_exists('callback', $clickData)) {
      $clickData['callback'] = 'NO_CALLBACK';
    }
    $this->adService->cacheAdsClicks($clickData);
  }
}