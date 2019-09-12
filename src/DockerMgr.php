<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 12.09.19
 * Time: 13:58
 */

namespace Rudl;


use Phore\FileSystem\PhoreFile;

class DockerMgr
{



    public function dockerLogin(string $user, string $pass, string $registryHost)
    {
        phore_proc("sudo docker login -u :user --password-stdin :registry", [
            "registry" => $registryHost,
            "user" => $user
        ])->exec()->write($pass)->close()->wait();
    }


    public function stackDeploy ($stackName, PhoreFile $stackFile)
    {
        phore_exec("sudo docker stack deploy --prune --with-registry-auth :stackName -c :file", [
            "stackName" => $stackName,
            "file" => $stackFile->getUri()
        ]);
    }


}