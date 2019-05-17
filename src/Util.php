<?php

namespace doyzheng\generators;

use Yii;

/**
 * 工具类
 * Class Util
 * @package doyzheng\generators
 */
class Util
{
    
    /**
     * 获取全部数据表
     * @return array
     */
    public static function getDbTables()
    {
        static $tables = [];
        if (empty($tables)) {
            try {
                $list = Yii::$app->db->createCommand('SHOW tables')->queryAll();
                foreach ($list as $item) {
                    $tables[] = array_pop($item);
                }
            } catch (\Exception $exception) {
            }
        }
        return $tables;
    }
    
}
