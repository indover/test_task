<?php

namespace app\commands;

use Exception;
use app\service\ClickHouseService;
use app\service\FileService;
use yii\console\Controller;

class ReadLogController extends Controller
{
    private FileService $fileService;
    private ?ClickHouseService $clickHouseConnector = null;

    public function getClickHouseConnector(): ClickHouseService
    {
        if (!$this->clickHouseConnector) {
            $this->clickHouseConnector = new ClickHouseService();
        }

        return $this->clickHouseConnector;
    }

    /**
     * @param $id
     * @param $module
     */
    public function __construct(
        $id,
        $module,
    )
    {
        $this->fileService = new FileService();
        parent::__construct($id, $module);
    }

    /**
     * @throws Exception
     */
    private function getLastLine(): array|string
    {
        $this->fileService->checkFile(env('LOG_PATH'));

        $line = file(env('LOG_PATH'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($line !== false) {
            $lastLine = end($line);
            return ['date' => $this->findDateInLog($lastLine), 'lastLine' => $lastLine];
        } else {
            throw new Exception('Error reading the file.');
        }
    }

    public function findDateInLog($line): ?string
    {
        return $this->fileService->findDateInLog($line);
    }

    public function actionList()
    {
        $this->getClickHouseConnector()->createTable();
        $lastPosition = 0;
        while (true) {
            clearstatcache(true, env('LOG_PATH'));
            $currentSize = filesize(env('LOG_PATH'));

            if ($currentSize > $lastPosition) {
                $this->getClickHouseConnector()->addLog($this->getLastLine()['date'], $this->getLastLine()['lastLine']);
                $lastPosition = $currentSize;
            }

            sleep(1);
        }
    }
}