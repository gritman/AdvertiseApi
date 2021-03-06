<?php
/**
 * Created by PhpStorm.
 * User: wangliugang
 * Date: 2016/7/14
 * Time: 9:47
 */

namespace Api\Controller;

use Think\Controller;
use Api\Common\Util;
use Api\Service\AppOpenService;

class AppOpenController extends Controller
{
    protected $bf_app_key = '@bftjadsdk!';

    public function __construct()
    {
        parent::__construct();
    }

    public function dispatch()
    {
        $gameData = $this->requestToGameData($_GET);
        $signCheck = $this->signCheck($gameData);
        if ($_GET['sign'] != $signCheck) {
            Util::jsonEchoExit(4, "sign check error");
        }
        try {
            $adOpenService = new AppOpenService();
            $ret = $adOpenService->processOpen($gameData);
            if (is_string($ret)) {
                Util::jsonEchoExit(1, $ret);
            } else {
                Util::jsonEchoExit(0, 'success');
            }
        } catch (AdNormalException $e) {
            Util::jsonEchoExit(2, $e->errorMessage());
        }
    }

    private function signCheck($arr = [])
    {
        ksort($arr, SORT_STRING);
        $sign = "";
        foreach ($arr as $k => $v) {
            $sign .= urlencode($k) . "=" . urlencode($v) . '&';
        }
        $sign .= 'appkey=' . urlencode($this->bf_app_key);
        $signCheck = md5($sign);
        return $signCheck;
    }

    private function requestToGameData($getRequest)
    {
        $gameData = [
            'active_at' => $_GET['active_at'],
            'app_type' => $_GET['app_type'],
            'ad_id' => $_GET['ad_id'],
            'appid' => $_GET['appid'],
            'channel_id' => $_GET['channel_id'],
            'device_id' => $_GET['device_id'],
            'ip' => $_GET['ip'],
            'mac' => $_GET['mac'],
            'actived_time' => $_GET['actived_time'],
        ];
        return $gameData;
    }
}
