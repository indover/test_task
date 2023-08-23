<?php

namespace app\commands;

use app\service\ClickHouseService;
use DateTime;
use yii\console\Controller;
use yii\db\Exception;
use yii\helpers\BaseConsole;

class ClickHouseController extends Controller
{
    public $startDate;
    public $finishDate;
    private ?ClickHouseService $clickHouseConnector = null;

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
     * @param $actionID
     * @return array|string[]
     */
    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), [
            DateTime::createFromFormat('Y-m-d H:i:s', $this->startDate),
            DateTime::createFromFormat('Y-m-d H:i:s', $this->finishDate)
        ]);
    }

    /**
     * @return void
     */
    public function actionGetCount(): void
    {
        $this->stdout('Total count rows ===>> ' . $this->getClickHouseConnector()->findAll($this->getClickHouseConnector()->generateNewClient())->count() . PHP_EOL, BaseConsole::FG_GREEN);
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function actionAddTest(): void
    {
        $this->getClickHouseConnector()->addTestRaw($this->getClickHouseConnector()->generateNewClient());
    }
}