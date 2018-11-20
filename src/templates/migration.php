use yii\db\Migration;

class m<?= $date ?>_create_table_<?= $name ?> extends Migration
{
    
    public $tableName = '<?= $name?>';

    public function up()
    {
        $this->createTable($this->tableName, [
        <?php foreach($columns as $name => $value):?>
<?php if (is_int($name)):?>
    "<?= $value ?>",
<?php else:?>
    '<?= $name?>' => "<?= $value ?>",
<?php endif;?>
        <?php endforeach;?>], "<?= $options?>");
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }

}