<?php

namespace Codilar\Witch;

class Cli
{

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Checker
     */
    protected $checker;

    /**
     * @var Logger
     */
    protected $logger;

    private $inputDir;
    private $outputDir;

    /**
     * Cli constructor.
     * @param $inputDir
     * @param $outputDir
     */
    public function __construct($inputDir, $outputDir)
    {

        $this->inputDir = $inputDir;
        $this->outputDir = $outputDir;

        $this->file = new File();
        $this->checker = new Checker();
        $this->logger = new Logger();
    }

    public function execute()
    {
        $this->file->createDirectoryIfNotExists($this->inputDir);
        $this->file->createDirectoryIfNotExists($this->outputDir);
        foreach ($this->getInputFiles() as $inputFile) {
            $inputFile = realpath($inputFile);
            sleep(1);
            $this->processFile($inputFile);
        }
        $this->logger->log('DONE', true);
    }

    protected function processFile($file)
    {
        $this->logger->log(sprintf('Processing file "%s"', $file));
        $fileInfo = pathinfo($file);
        $outputPath = $this->outputDir . '/' . str_replace(' ', '_', $fileInfo['filename']);
        $outputProgressPath = $outputPath . '/progress';
        $outputPartPath = $outputPath . '/output';
        $this->file->createDirectoryIfNotExists($outputPath);
        $progress = $this->file->readFile($outputProgressPath, -1);
        if ($progress === 'DONE') {
            $this->logger->log(sprintf('File "%s" already processed', $file));
        } else {
            $progress = intval($progress);
            $urls = $this->file->readCsvFile($file);
            $totalUrls = count($urls);
            $urls = array_slice($urls, $progress + 1);
            $totalRemainingUrls = count($urls);
            $this->logger->log(sprintf('%s/%s URLs already processed', $totalUrls - $totalRemainingUrls, $totalUrls));
            foreach ($urls as $url) {
                $this->logger->log(sprintf('Processing URL "%s" [%s/%s]', $url['url'], $progress + 2, $totalUrls));
                $technology = $this->checker->check($url['url']);
                $this->file->appendCsv($outputPartPath, [
                    'url' => $url['url'],
                    'technology' => $technology
                ]);
                $progress++;
                $this->file->writeFile($outputProgressPath, $progress);
                if ($progress % 10 === 0) {
                    sleep(1);
                }
            }
            $this->logger->log(sprintf('File "%s" processed', $file), true);
            $this->file->rename($outputPartPath, $outputPartPath . '.csv');
            $this->file->writeFile($outputProgressPath, 'DONE');
        }
    }

    protected function getInputFiles()
    {
        $files = glob($this->inputDir . '/*.csv');
        return $files;
    }
}
