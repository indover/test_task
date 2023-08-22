<?php

use bashkarev\clickhouse\Migration;

class m230821_215149_log extends Migration
{

    public function up()
    {
        $this->db->createCommand()->createTable('log', [
            'id' => 'Int32',
            'start_date' => 'Date',
            'data' => 'Text',
            'date' => 'Date',
            'version' => 'String',
            'apply_time' => 'UInt32'
        ], 'ENGINE=ReplacingMergeTree(date, version, 8192, apply_time)')->execute();

        $time = time();
        $this->db->createCommand()->insert('log', [
            'id' => $time,
            'start_date' => date('Y-m-d', $time),
            'data' => '127.0.0.1 - - [21/Aug/2023:18:46:10 -0700] "GET /js/vendor/bootstrap/bootstrap.bundle.min.js.map HTTP/1.1" 200 331017 "-" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.>',
            'version' => '$version',
            'date' => date('Y-m-d', $time),
            'apply_time' => $time,
        ])->execute();
    }

    public function down()
    {
        echo "m230821_215149_log cannot be reverted.\n";

        return false;
    }

}