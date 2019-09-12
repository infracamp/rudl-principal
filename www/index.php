<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 26.07.18
 * Time: 15:43
 */

namespace App;

use Phore\Core\Helper\PhoreSecretBoxSync;
use Phore\Letsencrypt\PhoreLetsencryptModule;
use Phore\Letsencrypt\PhoreSecureCertStore;
use Phore\MicroApp\App;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;
use Phore\VCS\VcsFactory;
use Phore\VCS\VcsRepository;
use Rudl\Cloudfront;
use Rudl\Config;
use Rudl\Ctrl\CloudfrontCertCtrl;
use Rudl\Ctrl\CloudfrontCtrl;
use Rudl\Ctrl\RepoPushHookCtrl;
use Rudl\DeployManager;
use Rudl\DockerMgr;
use Rudl\StackStatus;

require __DIR__ . "/../vendor/autoload.php";


$app = new App();
$app->setResponseHandler(new JsonResponseHandler());
$app->setOnExceptionHandler(new JsonExceptionHandler());
$app->acl->addRule(aclRule("*")->ALLOW());

$app->define("repo", function () : VcsRepository {
    $repo = new VcsFactory();
    $repo->setAuthSshPrivateKey(phore_file("/mnt/.ssh/id_ed25519")->get_contents());

    return $repo->repository("/mnt/repo", CONF_REPO_URL);
});

$app->define("config", function () : Config {
    return new Config("/mnt/repo", CONF_CLUSTER_NAME);
});

$app->define("certStore", function () : PhoreSecureCertStore {
    return new PhoreSecureCertStore(phore_file(CONF_PRINCIPAL_SECRET)->get_contents());
});

$app->define("cloudfront", function(PhoreSecureCertStore $certStore) : Cloudfront {
    return new Cloudfront($certStore);
});

$app->define("dockerMgr", function () : DockerMgr {
    return new DockerMgr();
});

$app->define("deployManager", function (Config $config, DockerMgr $dockerMgr, StackStatus $stackStatus) : DeployManager {
    return new DeployManager($config, $dockerMgr, $stackStatus);
});

$app->define("stackStatus", function () {
    return new StackStatus(phore_file("/tmp/stack-status.json"));
});


$app->addModule(new PhoreLetsencryptModule());

$app->addCtrl(RepoPushHookCtrl::class);
$app->addCtrl(CloudfrontCtrl::class);
$app->addCtrl(CloudfrontCertCtrl::class);

$app->router->onGet("/", function (VcsRepository $repo, Config $config, StackStatus $stackStatus) {
    return [
        "success" => true,
        "cluster-name" => CONF_CLUSTER_NAME,
        "msg" => "rudl-principal ready",

        "host" => gethostname(),
        "ssh-public-key" => trim(phore_file("/mnt/.ssh/id_ed25519.pub")->get_contents()),
        "repo-url" => CONF_REPO_URL,

        "info" => $config->getFileStatus(),
        "status" => $stackStatus->getData(),
        "repo-valid" => $repo->exists()
    ];
});


$app->serve();