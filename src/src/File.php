<?php

namespace Codilar\Witch;

class File {

    public function createDirectoryIfNotExists($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    public function readFile($file, $default = null)
    {
        if (!file_exists($file)) {
            return $default;
        }
        $fp = fopen($file, 'r');
        $contents = fread($fp, filesize($file));
        fclose($fp);
        return $contents;
    }

    public function readCsvFile($file)
    {
        if (!file_exists($file)) {
            return null;
        }
        $rows   = array_map('str_getcsv', file($file));
        $header = array_shift($rows);
        $csv    = [];
        foreach($rows as $row) {
            $csv[] = array_combine($header, $row);
        }
        return $csv;
    }

    public function writeFile($file, $data)
    {
        if (!file_exists($file)) {
            touch($file);
        }
        $fp = fopen($file, 'w');
        fwrite($fp, $data, strlen($data));
        fclose($fp);
    }

    public function appendCsv($file, $data)
    {
        $isNew = false;
        if (!file_exists($file)) {
            touch($file);
            $isNew = true;
        }
        $fp = fopen($file, 'a');
        if ($isNew) {
            fputcsv($fp, array_keys($data));
        }
        fputcsv($fp, $data);
        fclose($fp);
    }

    public function rename($old, $new)
    {
        if (file_exists($old)) {
            rename($old, $new);
        }
    }

    public function getAbsolutePath($path, $pwd)
    {
        if ($path[0] !== '/') {
            $path = $pwd . '/' . $path;
        }
        if (!file_exists($path)) {
            throw new \Exception(sprintf('No such file or directory %s', $path));
        }
        return realpath($path);
    }

}
