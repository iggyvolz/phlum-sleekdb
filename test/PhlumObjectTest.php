<?php

declare(strict_types=1);

namespace iggyvolz\phlum\SleekDB\test;

use iggyvolz\phlum\SleekDB\LoggingDriver;
use iggyvolz\phlum\SleekDB\SleekDBDriver;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PhlumObjectTest extends \iggyvolz\phlum\test\PhlumObjectTest
{
    public function setUp(): void
    {
        // Ensure data directory is empty - https://stackoverflow.com/a/3352564
        $dataDir = __DIR__ . "/../db";
        if(!is_dir($dataDir)) mkdir($dataDir);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dataDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        $this->driver = new SleekDBDriver($dataDir);
    }
}