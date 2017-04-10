<?php
/**
 * Created by PhpStorm.
 * User: wangliugang
 * Date: 2016/7/14
 * Time: 9:47
 */

namespace Api\Controller;

use Api\Controller\AdController;

class AdWeiboController extends AdController
{
    protected $conv_type = 'MOBILEAPP_ACTIVITE';

    public function __construct()
    {
        parent::__construct();
        $this->init(get_class($this));
    }

    public function predealAdsClickToClickData()
    {
        $clickData = [
            'timestamp' => $_GET['time'],
            'callback' => $_GET['callback'],
            'sdkid' => $this->ad_sdkid,
            'appid' => $_GET['appid']
        ];
        if(array_key_exists('imei', $_GET)) {
            $clickData['imei'] =  $_GET['imei'];
            $clickData['app_type'] = '0';
            $clickData['mid'] = $this->adProcessor->genMidFromClick($clickData['app_type'], $_GET['imei'], 'no_mac');
        }
        else {
            $clickData['idfa'] =  $_GET['idfa'];
            $clickData['app_type'] = '1';
            $clickData['mid'] = $this->adProcessor->genMidFromClick($clickData['app_type'], $_GET['idfa'], 'no_mac');
        }
        return $clickData;
    }

    protected function fillGameData(&$gameData, $dbConfig, $cacheVal)
    {
        $gameData['active_at'] = intval($_GET['active_at']);
        $cacheValArr = explode('____', $cacheVal);
        $gameData['callback'] = $cacheValArr[0];
    }
}
