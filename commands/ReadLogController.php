<?php

namespace app\commands;

use app\service\ClickHouseService;
use app\service\LogService;
use yii\console\Controller;
use yii\helpers\BaseConsole;

class ReadLogController extends Controller
{
    /**
     * @var LogService
     */
    private LogService $fileService;

    /**
     * @var ClickHouseService|null
     */
    private ?ClickHouseService $clickHouseConnector = null;

    /**
     * @param $id
     * @param $module
     */
    public function __construct(
        $id,
        $module,
    )
    {
        $this->fileService = new LogService();
        parent::__construct($id, $module);
    }

    /**
     * @return ClickHouseService
     */
    public function getClickHouseConnector(): ClickHouseService
    {
        if (!$this->clickHouseConnector) {
            $this->clickHouseConnector = new ClickHouseService();
        }

        return $this->clickHouseConnector;
    }

    /**
     * @return mixed
     */
    public function actionList(): mixed
    {
        while (true) {
            $startTime = microtime(true);

            $this->stdout('Total count raws from file ===>> ' . count(file(env('LOG_PATH'))) . PHP_EOL, BaseConsole::FG_GREEN);

            $data = $this->getClickHouseConnector()->findByColumn('data')->rows();

            $this->stdout('Total count rows from DB ===>> ' . count($data) . PHP_EOL, BaseConsole::FG_GREEN);

            $newData = $this->fileService->findNewData($data, file(env('LOG_PATH')));

            $this->stdout('Total count rows have to insert ===>> ' . count($newData) . PHP_EOL, $this->checkColor(count($newData)));

            $this->getClickHouseConnector()->addLog($newData);

            $this->stdout('Time spent ===>> ' . round(((microtime(true) - $startTime)), 2) . ' sec' . PHP_EOL, BaseConsole::FG_YELLOW);

            sleep(10);
        }
    }

    /**
     * @param int $count
     * @return int
     */
    private function checkColor(int $count): int
    {
        if ($count > 0) {
            return BaseConsole::FG_RED;
        }
        return BaseConsole::FG_GREEN;
    }
}