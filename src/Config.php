<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 12.09.19
 * Time: 14:03
 */

namespace Rudl;


use Phore\FileSystem\PhoreDirectory;
use Phore\FileSystem\PhoreUri;

class Config
{

    private $rootDir;
    private $clusterName;

    const STACK_D = "/stack.d";
    const CLOUDFRONT_D = "/cloudfront.d";
    const RUDL_CONFIG = "/rudl-config.yml";

    public function __construct($rootDir, $clusterName)
    {
        $this->rootDir = $rootDir;
        $this->clusterName = $clusterName;
    }


    public function getClusterRootDir() : PhoreDirectory
    {
        return phore_dir($this->rootDir . "/" . $this->clusterName);
    }

    public function getFileStatus()
    {
        $clusterDir = $this->getClusterRootDir();
        $files = [
            "/{$this->clusterName}" => $clusterDir->exists() ? "OK" : "MISSING",
            "/{$this->clusterName}/cloudfront.d/" => $clusterDir->withSubPath(self::CLOUDFRONT_D)->exists() ? "OK" : "MISSING",
            "/{$this->clusterName}/stack.d/" => $clusterDir->withSubPath(self::STACK_D)->exists() ? "OK" : "MISSING",
            "/{$this->clusterName}/rudl-config.yml" => $clusterDir->withSubPath(self::RUDL_CONFIG)->exists() ? "OK" : "MISSING",
        ];
        return $files;
    }


    public function getConfigFile() : array
    {
        return $this->getClusterRootDir()->withSubPath(self::RUDL_CONFIG)->assertFile()->get_yaml();
    }


    public function getStacks () : array
    {
        $generator = $this->getClusterRootDir()->withSubPath(self::STACK_D)->assertDirectory()->genWalk("*.yml");

        $stacks = [];
        foreach ($generator as $file) {
            $file = $file->assertFile();
            $stacks[$file->getFilename()] = $file;
        }
        return $stacks;
    }


}