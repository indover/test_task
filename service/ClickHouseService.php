<?php

namespace app\service;

use ClickHouseDB\Client;
use ClickHouseDB\Statement;
use DateTime;
use yii\db\Exception;
use yii\di\Instance;

class ClickHouseService
{
    private Client $clickHouseClient;

    public function __construct()
    {
        $this->clickHouseClient = $this->generateNewClient();
    }

    public function generateNewClient(): Client
    {
        $config = [
            'host' => env('CH_HOST'),
            'port' => (int)env('CH_PORT'),
            'database' => env('CH_DATABASE'),
            'username' => env('CH_USERNAME'),
            'password' => env('CH_PASSWORD')
        ];


        return new Client($config);
    }

    public function createTable(): void
    {
        $client = $this->generateNewClient();
        $client->write("
            CREATE TABLE IF NOT EXISTS log (
            id Int32,
            start_date Date,
            data Text
        )
        ENGINE = MergeTree
        PRIMARY KEY (id)
        ");
    }

    public function findAll(Client $client): Statement
    {
        return $client->select("SELECT * FROM log");
    }

    public function findByDates(Client $client, $startDate, $finishDate): Statement
    {
        $binds = [
            'a' => $startDate,
            'b' => $finishDate
        ];
        $sql = "SELECT * FROM log WHERE start_date BETWEEN (:a) AND (:b)";
        return $client->select($sql, $binds);
    }

    public function addTestRaw(Client $client): void
    {
        $client->insert('log', [
            [time(), date('Y-d-m H:i:s'), '127.0.0.1 - - [16/Aug/2023:18:46:10 -0700] "GET /js/vendor/bootstrap/bootstrap.bundle.min.js.map HTTP/1.1" 200 331017 "-" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.>', date('Y-m-d', time()), null, time()]
        ]);
    }

    public function addLog(string $date, string $data): void
    {
        $client = $this->generateNewClient();
        $client->insert('log', [
            [time(), $date, $data, date('Y-m-d', time()), null, time()]
        ]);
    }

    /**
     * @throws Exception
     */
    public function checkDates($startDate, $finishDate): void
    {
        if (DateTime::createFromFormat('Y-m-d', $startDate) instanceof DateTime
        AND DateTime::createFromFormat('Y-m-d', $finishDate) instanceof DateTime) {
            return;
        }

        throw new Exception('Not valid date. Date format: YYYY-MM-DD');
    }

    public function prepareSelect($startDate, $finishDate): Statement
    {
        $client = $this->generateNewClient();
        $client->enableQueryConditions();

        return $this->findByDates($client, $startDate, $finishDate);
    }
}