<?php

namespace app\commands;

use app\service\ClickHouseService;
use ClickHouseDB\Statement;
use DateTime;
use JetBrains\PhpStorm\NoReturn;
use yii\console\Controller;
use yii\db\Exception;
use yii\helpers\BaseConsole;
use function Symfony\Component\String\s;

class ClickHouseController extends Controller
{
    private ?ClickHouseService $clickHouseConnector = null;

    public function getClickHouseConnector(): ClickHouseService
    {
        if (!$this->clickHouseConnector) {
            $this->clickHouseConnector = new ClickHouseService();
        }

        return $this->clickHouseConnector;
    }

    public $startDate;
    public $finishDate;

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), [
            DateTime::createFromFormat('Y-m-d H:i:s', $this->startDate),
            DateTime::createFromFormat('Y-m-d H:i:s', $this->finishDate)
        ]);
    }

    public function actionConnection(): void
    {
        $this->getClickHouseConnector()->createTable();
    }

    #[NoReturn] public function actionGetCount(): void
    {
        $this->stdout('Total count rows ===>> ' . $this->getClickHouseConnector()->findAll($this->getClickHouseConnector()->generateNewClient())->count() . PHP_EOL, BaseConsole::FG_GREEN);
    }

    public function actionData(): void
    {
        print_r($this->getClickHouseConnector()->findAll($this->getClickHouseConnector()->generateNewClient())->info());
    }

    /**
     * @throws Exception
     */
    public function actionRaws($startDate, $finishDate): void
    {
        $this->getClickHouseConnector()->checkDates($startDate, $finishDate);

        print_r($this->getClickHouseConnector()->prepareSelect($startDate, $finishDate)->rows());
    }

    /**
     * @throws Exception
     */
    public function actionCount($startDate, $finishDate): void
    {
        $this->getClickHouseConnector()->checkDates($startDate, $finishDate);

        $this->stdout('Total count rows between input dates ===>> ' . $this->getClickHouseConnector()->prepareSelect($startDate, $finishDate)->count() . PHP_EOL, BaseConsole::FG_GREEN);
    }


    /** add test data */
    public function actionAddTest(): void
    {
        $this->getClickHouseConnector()->addTestRaw($this->getClickHouseConnector()->generateNewClient());
    }
}