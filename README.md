### yii2文件生成类
```
Migration 类使用方法 生成迁移数据库文件 
    $migration =  new Migration();
    // 生成user表migrate文件
    $migration->generator('user');
    // 批量生成, 参数为空时生成全部
    $migration->batchGenerator();
```
