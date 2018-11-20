<?php

namespace doyzheng\generators;

use Yii;
use yii\helpers\FileHelper;

/**
 * Generate the migration file
 * Class Migrations
 * @package app\components\generators
 */
class Migration
{
    
    /**
     * @var string 生成目录
     */
    public $savePath;
    
    /**
     * @var string 模板文件名
     */
    public $template;
    
    /**
     * Migration constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        Yii::configure($this, $config);
        if (!$this->savePath) {
            $this->savePath = __DIR__ . '/../../../../migrations';
            try {
                FileHelper::createDirectory($this->savePath);
            } catch (\Exception $exception) {
            }
        }
        if (!$this->template) {
            $this->template = __DIR__ . '/templates/migration.php';
        }
    }
    
    /**
     * 批量生成
     * @param array $tables
     * @return bool
     */
    public function batchGenerator($tables = [])
    {
        if (empty($tables)) {
            $tables = $this->getTables();
        }
        foreach ($tables as $table) {
            $this->generator($table);
        }
        return true;
    }
    
    /**
     * 根据数据表名称生成文件
     * @param $tableName
     * @return bool
     */
    public function generator($tableName)
    {
        $date        = gmdate('ymd_His');
        $outFilename = $this->savePath . '/m' . $date . '_create_table_' . $tableName . '.php';
        if ($tableInfo = self::getCreateInfo($tableName)) {
            $params = [
                'date'    => $date,
                'name'    => $tableInfo['name'],
                'columns' => $tableInfo['columns'],
                'options' => $tableInfo['options'],
            ];
            return $this->render($outFilename, $params);
        }
        return false;
    }
    
    /**
     * 渲染模板并保存到文件
     * @param $outFilename
     * @param $params
     * @return bool
     */
    private function render($outFilename, $params)
    {
        try {
            if (is_file($this->template)) {
                $code = "<?php \n" . Yii::$app->view->renderFile($this->template, $params);
                FileHelper::createDirectory(dirname($outFilename));
                return file_put_contents($outFilename, $code) != false;
            }
        } catch (\Exception $exception) {
        }
        return false;
    }
    
    /**
     * 获取全部数据表
     * @return array
     */
    private function getTables()
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
    
    /**
     * @param $name
     * @return array
     */
    private function getCreateInfo($name)
    {
        $tableInfo = [
            'name'    => $name,
            'columns' => [],
            'options' => '',
        ];
        $createSql = $this->getCreateSql($name);
        $lines     = explode("\n", $createSql);
        array_shift($lines);
        $tableInfo['options'] = trim(substr(array_pop($lines), 1));
        $tableInfo['columns'] = $this->parseSqlLines($lines);
        return $tableInfo;
    }
    
    /**
     * 获取表单创建Sql语句
     * @param $name
     * @return string
     */
    private function getCreateSql($name)
    {
        try {
            $createInfo = Yii::$app->db->createCommand("show create table {$name}")->queryOne();
            return isset($createInfo['Create Table']) ? $createInfo['Create Table'] : '';
            
        } catch (\Exception $exception) {
        
        }
        return '';
    }
    
    /**
     * 解析每行SQL语句转换到数组格式
     * @param array $lines
     * @return array
     */
    private function parseSqlLines($lines)
    {
        $columns = [];
        foreach ($lines as $line) {
            // 去掉最后一个逗号
            $line = trim(preg_replace('/(.*),$/', '$1', $line));
            if (preg_match('/^`(.*)`(.*)$/', $line, $matches)) {
                $columns[$matches[1]] = trim($matches[2]);
            } else {
                $columns[] = $line;
            }
        }
        return $columns;
    }
 
}
