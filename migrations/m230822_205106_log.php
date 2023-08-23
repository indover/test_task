<?php

use bashkarev\clickhouse\Migration;

class m230822_205106_log extends Migration
{

    public function up()
    {
        $this->db->createCommand("
        CREATE TABLE IF NOT EXISTS log (
            id Int32,
            start_date Date,
            data Text
        )
        ENGINE = MergeTree
        PRIMARY KEY (id)
        ")->execute();
    }

    public function down()
    {
        echo "m230822_205106_log cannot be reverted.\n";

        return false;
    }

}