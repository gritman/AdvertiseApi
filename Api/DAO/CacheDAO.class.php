<?php
  /**
   * Created by PhpStorm.
   * User: Administrator
   * Date: 2017/3/21
   * Time: 16:21
   */

namespace Api\DAO;

use Think\Cache\Driver;
use Api\Common\Util;

class CacheDAO
{
  static public $redisOption = [
				'type' => 'redis',
				'host' => '120.27.148.158',
				'port' => '6379',
				'expire' => 604800
				];

  static public $separator = '_BIANFENG_';

  protected $redis;

  public function __construct()
  {
    $this->redis = new Driver\Redis(CacheDAO::$redisOption);
  }

  public function isCached($cacheKey)
  {
    Util::log("isCached: $cacheKey", "");
    return $this->redis->exists($cacheKey);
  }

  public function getCacheValue($cacheKey)
  {
    Util::log("getCacheValue: $cacheKey", "");
    if ($this->isCached($cacheKey)) {
      return $this->redis->get($cacheKey);
    } else {
      Util::log("getCacheValue not exist", "");
      return null;
    }
  }

  public function delCacheKey($cacheKey) {
    Util::log("delCacheKey: $cacheKey", "");
    $this->redis->del($cacheKey);
  }

  public function setCacheKeyValue($cacheKey, $cacheValue) {
    Util::log("setCacheKeyValue: $cacheKey, $cacheValue", "");
    $this->redis->set($cacheKey, $cacheValue);
  }
}