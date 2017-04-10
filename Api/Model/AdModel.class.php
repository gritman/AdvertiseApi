<?php
/**
 * Created by PhpStorm.
 * User: wangliugang
 * Date: 2016/7/14
 * Time: 10:30
 */

namespace Api\Model;

use Think\Model;
use Admin\Model\Processor\AdService;

class  AdModel extends Model
{
    // 测试环境
    protected $connection = array(
        'db_type' => 'mysql',
        'db_user' => 'dev',
        'db_pwd' => 'dev123456',
        'db_host' => '127.0.0.1',
        'db_port' => '3306',
        'db_name' => 'test_puma_ad',
        'db_charset' => 'utf8',
    );
    // 部署环境
    protected $connection_deploy = array(
        'db_type' => 'mysql',
        'db_user' => 'mbuser',
        'db_pwd' => 'SZ:kc_,',
        'db_host' => '115.29.207.25',
        'db_port' => '3306',
        'db_name' => 'adver',
        'db_charset' => 'utf8',
    );
    protected $autoCheckFields = false;
    protected $tablePrefix = 'ad_';
    protected $tableName;

    public function __construct($tableName = '')
    {
        parent::__construct();
        if (!$tableName) {
            return NULL;
        }
        if ('open' == $tableName) {
            $trueTableName = 'ad_open_' . date('Ym');
            $Model = new AdModel();
            $sql = 'Create Table If Not Exists ' . $trueTableName . ' Like ad_open';
            $Model->execute($sql);
            $tableName = 'open_' . date('Ym');
        }
        if ('click' == $tableName) {
            $trueTableName = 'ad_click_' . date('Ym');
            $Model = new AdModel();
            $sql = 'Create Table If Not Exists ' . $trueTableName . ' Like ad_click';
            $Model->execute($sql);
            $tableName = 'click_' . date('Ym');
        }
        if ('active' == $tableName) {
            $trueTableName = 'ad_active_' . date('Ym');
            $Model = new AdModel();
            $sql = 'Create Table If Not Exists ' . $trueTableName . ' Like ad_active';
            $Model->execute($sql);
            $tableName = 'active_' . date('Ym');
        }
        $this->tableName = $tableName;
    }
}