<?php
/**
 * Created by PhpStorm.
 * User: wangliugang
 * Date: 2016/11/23
 * Time: 9:47
 */

namespace Api\Controller;

use Api\Controller\AdController;
use Api\Common\Util;

class AdHeadlineController extends AdController
{
    public function __construct()
    {
        parent::__construct();
        $this->init(get_class($this));
    }

    public function predealAdsClickToClickData()
    {
        $clickData = [
            'appid' => $_GET['adid'], // 广告计划ID，就是表里的AdAppId
            'cid' => $_GET['cid'], // 广告创意ID，可能是advertise_id
            'mac' => $_GET['mac'],
            'app_type' => $_GET['os'], // 0 android 1 ios 2 wp 3 others
            'click_time' => $_GET['timestamp'],
            'callback' => $_GET['callback'],
            'sdkid' => $this->adId
        ];
	Util::log("sdkid is: ".$clickData['sdkid'], "");
        if ($clickData['app_type'] == '1') { // ios
            $clickData['mid'] = $this->adService->genMidFromClick($clickData['app_type'], $_GET['idfa'], $clickData['mac']);
        } else { // android
            $clickData['mid'] = $this->adService->genMidFromClick($clickData['app_type'], $_GET['imei'], $clickData['mac']);
        }
        return $clickData;
    }
}
