<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/21
 * Time: 20:28
 */

namespace Api\Service;

use Api\DAO\CacheDAO;
use Api\DAO\ConfigDAO;
use Api\DAO\RecordDAO;
use Api\Service\AdService;

class AppOpenService
{
    protected $configDAO;
    protected $cacheDAO;
    protected $recordDAO;
    protected $adId = null;
    protected $adName = null;

    public function __construct()
    {
        $this->recordDAO = new RecordDAO();
        $this->configDAO = new ConfigDAO();
        $this->cacheDAO = new CacheDAO();
    }

    public function processOpen($gameData)
    {
        $configList = $this->configDAO->getConfigList($gameData['appid'], $gameData['app_type']);
        if (empty($configList)) {
            return sprintf("cannot find config [appid %s, app_type %s]", $gameData['appid'], $gameData['app_type']);
        }
        $this->genCacheKey($configList, $gameData);
        $cacheData = $this->getMatchedCacheValue($configList);
        if ($cacheData == null) {
            $this->recordWhenCnannotFindConfig($gameData);
            return sprintf("cannot find cache [appid %s, app_type %s]", $gameData['appid'], $gameData['app_type']);
        }

        $adService = AdService::factory(AdService::getAdNameByAdId($cacheData['adId']));
        $adService->initConfig($gameData['appid'], $cacheData['adId'], $cacheData['ideaId']);
        $adService->fillGameData($cacheData, $gameData);

        $thirdRet = $adService->activedReturn($gameData, $cacheData);

        $this->cacheDAO->delCacheKey($cacheData['cacheKey']);
        $this->recordDAO->addActiveModel($gameData, $thirdRet);
        $this->recordDAO->addOpenModel($gameData, RecordDAO::$openStatusEnum['ACTIVATED']);
    }

    private function recordWhenCnannotFindConfig($gameData)
    {
        $gameData['mid'] = md5(strtolower($gameData['device_id']));
        $this->recordDAO->addOpenModel($gameData, RecordDAO::$openStatusEnum['NO_CACHED']);
    }

    private function getMatchedCacheValue(&$configList)
    {
        foreach ($configList as $element) {
            $cacheKey = $element['cacheKey'];
            $cacheValue = $this->cacheDAO->getCacheValue($cacheKey);
            if ($cacheValue != null) {
                $element['cacheValue'] = $cacheValue;
                return $element;
            }
        }
        return null;
    }

    private function genCacheKey(&$configList, $gameData)
    {
        foreach ($configList as &$element) {
            $adId = $element['adId'];
            $ideaId = $element['ideaId'];
            $adService = AdService::factory(AdService::getAdNameByAdId($adId));
            $mid = $adService->genMidFromOpen($gameData['app_type'], $gameData['device_id'], $gameData['mac']);
            $cacheKey = AdService::genCacheKey($adId, $ideaId, $mid);
            $element['cacheKey'] = $cacheKey;
            $element['cacheValue'] = null;
        }
    }
}