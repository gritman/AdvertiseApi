<?php
/**
 * Created by PhpStorm.
 * User: wangliugang
 * Date: 2016/7/14
 * Time: 9:47
 */

namespace Api\Controller;

use Api\Controller\AdController;

class AdvertisingController extends AdController
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
            'mid' => $_GET['muid'],
            'click_time' => $_GET['click_time'],
            'appid' => $_GET['appid'],
            'clickid' => $_GET['click_id'],
            'app_type' => $_GET['app_type'],
            'advertise_id' => $_GET['advertiser_id'],
            'sdkid' => $this->adId
        ];
        if ($clickData['app_type'] == "ios") {
            $clickData['app_type'] = '1';
        }
        $clickData['callback'] = 'NOCALLBACK';
        return $clickData;
    }

    protected function fillGameData(&$gameData, $dbConfig, $cacheVal)
    {
        $gameData['active_at'] = intval($_GET['active_at'] / 1000);
        $gameData['advertise_id'] = $dbConfig['advertise_id'];
        $gameData['app_key'] = $dbConfig['app_key'];
        $gameData['app_val'] = $dbConfig['app_val'];
    }
}
