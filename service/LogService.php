<?php

namespace app\service;

use Exception;

class LogService
{
    /**
     * @throws Exception
     */
    public function checkFile(string $path): string
    {
        if (!$path) {
            throw new Exception('File path requires.');
        }

        if (!file_exists($path)) {
            throw new Exception('File does not exist');
        }

        return $path;
    }

    /**
     * @param $data
     * @param $file
     * @return array
     */
    public function findNewData($data, $file): array
    {
        $resetData = array_map(function($item) {
            return reset($item);
        }, $data);

        return array_diff($file, $resetData);
    }
}