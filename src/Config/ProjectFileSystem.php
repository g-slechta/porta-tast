<?php


namespace App\Config;


use http\Exception\RuntimeException;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem;
use SplFileInfo;

class ProjectFileSystem extends Filesystem
{
    private string $projectDir;

    public function __construct(
        string $projectDir
    )
    {
        $this->projectDir = $projectDir;
    }

    public function getRootDir(): string
    {
        return $this->createDir();
    }

    public function getTmpDir(): string
    {
        return $this->createDir('var', 'tmp');
    }


    public function createDir(string ...$dirs): string
    {
        $path = $this->projectDir;
        foreach ($dirs as $dir) {
            $path .= DIRECTORY_SEPARATOR . $dir;
            if (!is_dir($path)) {
                if (false === @mkdir($path, 0777, true) && !is_dir($path)) {
                    throw new RuntimeException(sprintf("Unable to create directory: %s\n", $path));
                }
            } elseif (!is_writable($path)) {
                throw new RuntimeException(sprintf("Unable to write in directory: %s\n", $path));
            }
        }
        return $path;
    }

    public function getDir(string ...$dirs): SplFileInfo
    {
        return new SplFileInfo($this->createDir(...$dirs));

    }
}