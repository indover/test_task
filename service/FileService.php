<?php

namespace app\service;

use Exception;

class FileService
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

    public function findDateInLog($line): ?string
    {
        if ($line !== false) {
            if (preg_match('/\[(\d{2}\/[A-Za-z]{3}\/\d{4}:\d{2}:\d{2}:\d{2} -\d{4})\]/', $line, $matches)) {
                $dateStr = $matches[1];
                $date = \DateTime::createFromFormat('d/M/Y:H:i:s O', $dateStr);
                if ($date !== false) {
                    return $date->format('Y-m-d H:i:s');
                }
            }
        }

        return false;
    }
}