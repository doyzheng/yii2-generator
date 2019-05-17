<?php

namespace doyzheng\generators;

use yii\base\Component;
use yii\gii\generators\model\Generator;
use yii\helpers\FileHelper;

/**
 * 生成数据表对应的模型
 * Class Model
 * @package doyzheng\generators
 */
class Model
{
    
    private $config = [
        'ns'                                 => 'app\models',
        'tableName'                          => '',
        'modelClass'                         => '',
        'baseClass'                          => 'yii\db\ActiveRecord',
        'generateLabelsFromComments'         => true,
        'useTablePrefix'                     => true,
        'generateRelations'                  => Generator::RELATIONS_ALL,
        'generateRelationsFromCurrentSchema' => true,
        'standardizeCapitals'                => false,
        'useSchemaName'                      => true,
        'queryNs'                            => 'app\models',
        'generateQuery'                      => false,
        'queryBaseClass'                     => 'yii\db\ActiveQuery',
    ];
    
    /**
     * Model constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $name => $value) {
            if (isset($this->config[$name])) {
                $this->config[$name] = $value;
            }
        }
    }
    
    /**
     * @param array $tables
     * @return bool
     */
    public function batchGenerator($tables = [])
    {
        if (empty($tables)) {
            $tables = Util::getDbTables();
        }
        foreach ($tables as $table) {
            $this->generator($table);
        }
        return true;
    }
    
    /**
     * @param $name
     * @return bool|int
     */
    public function generator($name)
    {
        $tables = Util::getDbTables();
        if (!in_array($name, $tables)) {
            return false;
        }
        $config = $this->config;
        
        $config['tableName']  = $name;
        $config['modelClass'] = \yii\helpers\Inflector::camelize($name) . 'Model';
        
        $g = new Generator($config);
        
        $files = $g->generate();
        
        foreach ($files as $file) {
            FileHelper::createDirectory(dirname($file->path));
            return file_put_contents($file->path, $file->content);
        }
        return false;
    }
    
}
