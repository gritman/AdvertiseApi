<?php
  /**
   * Created by PhpStorm.
   * User: Administrator
   * Date: 2017/3/21
   * Time: 16:39
   */

namespace Api\Common;

use Think\Log;

class Util
{
  static private $env = 'develop'; // 'deploy'

  static public function getBaseUrl()
  {
    if (Util::$env == 'develop') {
      return Util::$developConfig['base_url'];
    }
    if (Util::$env == 'deploy') {
      return Util::$deployConfig['base_url'];
    }
  }
  
  // Api\Controller\AdHeadlineController to AdHeadline
  static public function controllerNameToAdName($controllerName) {
    $explodedArray = explode("\\", $controllerName);
    $adName = strstr($explodedArray[2], 'Controller', TRUE);
    return $adName;
  }
  // AdHeadline to Api\Service\AdHeadlineService
  static public function adNameToServiceName($adName) {
    return 'Api\\Service\\'.$adName."Service";
  }
  static public function log($msg, $imeiOrIdfa)
  {
    // if ($imeiOrIdfa == '99000562927063'
    //	|| $imeiOrIdfa == 'E218D431-5073-4B1D-B97D-8D10D3AB7CD2'
    //	|| $imeiOrIdfa == md5('99000562927063')
    //	|| $imeiOrIdfa == md5('E218D431-5073-4B1D-B97D-8D10D3AB7CD2')
    //	|| $imeiOrIdfa == 'ba8193cdc2b94265ae5ef6c78b90cefb'
    //  ) 
    {
      $currTime = date("Y-m-d h:i:sa");
      \Think\Log::record("AdApi::log($currTime): " . $msg, 'INFO');
    }
  }

  static public function valueOrNull($key, $arr)
  {
    return array_key_exists($key, $arr) ? $arr[$key] : 'NULL';
  }

  static public function retJson($ret = 0, $msg = 'ok')
  {
    $return = [
	       'ret' => $ret,
	       'msg' => $msg
	       ];
    $jsonReturn = json_encode($return);
    return $jsonReturn;
  }

  static public function jsonEchoExit($ret = 0, $msg = 'ok')
  {
    $return = [
	       'ret' => $ret,
	       'msg' => $msg
	       ];
    $jsonReturn = json_encode($return);
    echo $jsonReturn;
    exit;
  }

  static public $developConfig = array(
				       'base_url' => 'http://pumatest.linkpc.net',
				       );
  static public $deployConfig = array(
				      'base_url' => 'http://puma.bfun.cn',
				      );
}